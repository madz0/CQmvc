<?php

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