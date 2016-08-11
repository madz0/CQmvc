<?php

interface ICache {
	
	public function get($key);
	
	public function update($key, $data);
	
	public function delete($key);
}