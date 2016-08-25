<?php
if(@chdir('../')) {
	@include '404.php';
} else {
	@include '../404.php';
}
?>