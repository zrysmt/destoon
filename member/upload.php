<?php
if(@chdir('../')) {
	@include 'upload.php';
} else {
	@include '../upload.php';
}
?>