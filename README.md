## Running the API

### 1. Add your DB credentials to the `.env` file

### 2. Run the following commands in the terminal to install the dependencies, run migrations, and serve the API:

-   `$composer install` to install the composer dependencies
-   `$npm install` to install the NPM dependencies
-   `$php artisan migrate`
-   `$php artisan serve`

### 3. To run the automated test suites:

-   `$php artisan test`

### 4. No Postman 👎

I hate Postman as much as any sane person would do, so instead you can just run:

    php artisan api:paths {source} {destination}

Make sure to have the server up and running before executing the command.

---

## Updates:

The final implementation of the locking mechanism works as follows:

### Locking

The current feature’s workflow for locking is:

1. User intends to prohibit (lock) a specific action
2. User uses `InteractsWithLocks` in the desired class
3. User calls `$this->lock('key')` with any desired key that represents the action
    1. key may be (and preferably) obtained using `getLockCacheKey()`
4. `lock()` will check if that specific action is already present in the cache
    1. If so, it throws an exception
    2. else, we proceed with the normal workflow
5. The `lock()`method now acquires a lock on that key to prevent racing conditions from overwriting it. They lock is obtained from `Cache::lock()` and with a time to live the same as the one provided for the action to be locked.
6. The `lock()` method puts a cache entry with the key and TTL that the user provided
    1. the entry is `key: $cacheKey value: lock->owner()`
    2. The reason we’re storing the lock’s owner is to be able to pass the lock to other processes such as, and specifically, `unlock()`
7. After the TTL expires, both locks will be released, which are:
    1. The one that locks the action, and:
    2. the one that locks other users from overwriting the lock key in Cache
    3. therefore, for every `lock()` call, there will be two corresponding cache entries, one to lock the action and one to prevent users from overwriting that lock in race conditions, both of which expiring at the same time.

This process ensures that the method is **_idempotent,_** meaning that race condition will not result in undesired side-effects on the server :)

### Unlocking:

1. The user calls `unlock()` and passes the cache key for the action
    1. This may be obtained again by calling `getLockCacheKey()`
2. The `unlock()` method will retrieve the owner of the lock that was put on the cache entry
3. Once the owner is retrieved, we can retrieve the corresponding lock
4. call `$lock->release()` to delete the lock
5. delete the cache entry by `Cache::forget($cacheKey)`

This ensures that there are no remaining locks after we delete the cache entry, also ensuring that there are no remaining dead locks (if we don’t release the locks, they remain dead until their expiry, and that time is unknown, could be from seconds to hours..)

Now we apply this mechanism to prevent our hidden village travelers from colliding into each other if they ever wanted to take the same route …
