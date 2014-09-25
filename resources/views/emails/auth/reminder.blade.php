<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Password Reset</h2>

		<div>
			To reset your password, complete this form: {{ url('password/reset', [$token]) }}.<br/>

			This link will expire in {{ config('auth.reminder.expire', 60) }} minutes.
		</div>
	</body>
</html>
