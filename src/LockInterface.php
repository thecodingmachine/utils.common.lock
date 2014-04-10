<?php
namespace Mouf\Utils\Common;

/**
 * Interface to be implemented by any object exposing a "lock" behaviour.
 * 
 * @author David Negrier
 */
interface LockInterface {
	
	/**
	 * Tries to acquire the lock.
	 * If $wait = true, the script will wait for the lock to be freed and acquire it.
	 * If $wait = false, the script will throw an exception if it fails acquiring the lock.
	 * 
	 * @param bool $wait If true, the script will wait until the lock is freed and will acquire the lock
	 * @throws LockException
	 */
	function acquireLock($wait = false);
	
	/**
	 * Releases the lock.
	 * Will throw a LockException if the lock was not acquired in the first place.
	 * 
	 * @throws LockException
	 */
	function releaseLock();
}