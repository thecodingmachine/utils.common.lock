<?php
namespace Mouf\Utils\Common;

/**
 * This class represents a lock.
 * Typically, you want to use locks when you want to be sure that 2 actions do not
 * happen at the same time. For instance, if you regularly schedul cron tasks,
 * you might want to be sure the last cron task finished before running the new one.
 * A lock can help you do that.
 * 
 * @author David Negrier
 */
class Lock implements LockInterface {
	
	/**
	 * Lock constructor
	 * @param string $fileName Locks are implemented using a temporary file (that will be created in the temporary directory). Please provide a unique name for that file.
	 */
	public function __construct($fileName = null) {
		$this->fileName = $fileName;
	}
	
	/**
	 * Locks are implemented using a temporary file (that will be created in the temporary directory)
	 * Please provide a unique name for that file.
	 * 
	 * @var string
	 */
	private $fileName;
	
	private $fpt;
	
	/**
	 * Tries to acquire the lock.
	 * If $wait = true, the script will wait for the lock to be freed and acquire it.
	 * If $wait = false, the script will throw an exception if it fails acquiring the lock.
	 * 
	 * @param bool $wait If true, the script will wait until the lock is freed and will acquire the lock
	 * @throws LockException
	 */
	public function acquireLock($wait = false) {
		
		if (empty($this->fileName)) {
			throw new LockException("You should provide a fileName in the instance of your lock.");
		}
		
		if (!file_exists(sys_get_temp_dir().'/'.$this->fileName)) {
			touch(sys_get_temp_dir().'/'.$this->fileName);
		}

		if ($this->fpt = fopen(sys_get_temp_dir().'/'.$this->fileName, 'r'))
		{
			if ($wait) {
				$lockMode = LOCK_EX;
			} else {
				$lockMode = LOCK_EX | LOCK_NB;
			}
			$boo = flock($this->fpt, $lockMode, $wait);

			if ($boo == false){
				// File is locked: the next process did not ended: we do not start
				throw new LockException('Could not acquire lock. Lock is in use', 1);
			}
		} else {
			throw new LockException('Could not acquire lock. A problem occured opening the file. Check the filename is valid.', 3);
		}
	}
	
	/**
	 * Releases the lock.
	 * Will throw a LockException if the lock was not acquired in the first place.
	 * 
	 * @throws LockException
	 */
	public function releaseLock() {
		if ($this->fpt == null) {
			throw new LockException('Could not release lock. Lock was not acquired in the first place', 2);
		}
		
		flock($this->fpt, LOCK_UN);
		fclose($this->fpt);
		
		$this->fpt = null;
	}
}
