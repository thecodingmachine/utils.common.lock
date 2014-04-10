Lock Manager
============

The LockManager package is a simple package that provides functions to get a lock.
Typically, you want to use locks when you want to be sure that 2 actions do not
happen at the same time. For instance, if you regularly schedule cron tasks,
you might want to be sure the last cron task is finished before running a new one.
A lock can help you do that.

The Lock class
--------------

The Lock class represents a lock. You can **acquire** a lock to lock the resource and **release** the lock
to set it free. Of course, if the lock has been already acquired by another user,
you won't be able to acquire it yourself. You can optionnaly wait for the lock
to be freed by the other process to acquire it yourself.

Internally, locks are acquired by putting a lock on a "file" that is hidden in the temp
directory. When creating a `Lock` instance in Mouf, you will therefore have to find a unique temp name
for that file.

If your PHP script crashes or exits without explicitly releasing the lock, the lock will be 
automatically released, so that other processes can use the lock.

Example
-------

A first example: trying to acquire a lock without waiting

```php
// You need to create an instance "myLock" of the Lock class in Mouf first.
$lock = Mouf::getMyLock();

// Try to acquire lock without waiting
try {
	$lock->acquireLock();
	// ... Do some stuff ...
	$lock->releaseLock();
} catch (LockException $e) {
	// The lock could not be acquired... Let's ignore this.
}
```

A second example: acquire a lock and wait if the lock is not available

```php
// You need to create an instance "myLock" of the Lock class in Mouf first.
$lock = Mouf::getMyLock();

// Try to acquire lock and wait if the lock is not available
$lock->acquireLock(true);
// ... Do some stuff ...
$lock->releaseLock();

```

An example without using Mouf or any other DI mechanism:


```php
$lock = new Lock("my_lock_name");

// Try to acquire lock without waiting
try {
	$lock->acquireLock();
	// ... Do some stuff ...
	$lock->releaseLock();
} catch (LockException $e) {
	// The lock could not be acquired... Let's ignore this.
}
```
