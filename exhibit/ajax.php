<?php
if(@chdir('../')) {
	@include 'ajax.php';
} else {
	@include '../ajax.php';
}
?>