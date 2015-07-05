<?php
/*
 * CQMvc A PHP MVC Framework
 *
 * Copyright 2015 by Mohamad Zeinali mohamad.basu@gmail.com
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
class Test extends Ctrl {
	
	
	public function index() {
	
		//How it handles views
		
		$this->view(new Index());
		$this->render();
		//or use this, it'll GZip the the output
		//$this->renderGz();
	}
	
	public function postTest(UserModel $myModel = null, $id = 0) {
		
		print "id: $id<br />";
		
		echo "Name $myModel->name <br />";
		
		echo "Children<br />";
				
		foreach ($myModel->children as $x) {
			
			echo "$x<br />";
		}
	}
	
	public function uploadTest(UserModel $myModel = null) {
		
		print $myModel->profileImage->name."<br />";
		
		/* @var $file FileBase */
		foreach ($myModel->postedFiles as $file) {
				
			echo "$file->name <br />";
		}
	}
	
	public function complexFormTest(array $myModels = null) {

		foreach ($myModels as $u) {
			
			echo "Name: $u->name <br />";
			
			echo "Profile Image Name ";
			print $u->profileImage->name."<br />";
			
			echo "Children:<ul>";
			
			/* @var $x ChildModel */
			foreach ($u->children as $x) {
					
				echo "<li>$x->name $x->lastName</li>";
			}
			print "</ul>";
			echo "Posted Files Name<ul>";
			
			/* @var $file FileBase */
			foreach ($u->postedFiles as $file) {
			
				print "$file->name <br />";
			}
			print "</ul>";
			echo "<hr />";
		} 
	}
	
	public function twoDinension(array $arr) {
		
		foreach ($arr as $ar) {
			
			foreach ($ar as $a) {
					
				print "<br />$a";	
			}	
		}
	}
	
	public function twoDinensionalUsers(array $users) {

		foreach ($users as $user) {

			/* @var $u UserModel */
			foreach ($user as $u) {
					
				print "<br />$u->name";
			}
		}
	}
	
	public function masterView() {
		
		$master = new MasterView();
		
		$master->header = $this->view(new HeaderView());
		
		$master->content = $this->view(new ContentView());
		
		$this->renderGz($master);
	}
}