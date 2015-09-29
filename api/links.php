<html>
	<body>
		<ul>
			<li><a href="/api/initialize">Initialize Database with Test Data</a></li>
			<li><a href="/api/login/employee">Login as employee</a></li>
			<li><a href="/api/login/manager">Login as manager</a></li>
		</ul>
		<ul>
			<li><a href="/api/shifts/1"><b>(Item #1)</b> View assigned shifts for employeeId = 1</a></li>
			<li><a href="/api/coworkers/1/1"><b>(Item #2)</b> View Co-Workers for employeeId = 1 and shiftId = 1</a></li>
			<li><a href="/api/summary/1"><b>(Item #3)</b> View Summary of Time Worked</a></li>
			<li><a href="/api/shifts/1?manager"><b>(Item #4)</b> View assigned shifts for employeeId = 1 with manager contact information</a></li>
			<li><a href="#" onclick="document.getElementById('item5').submit(); return false;"><b>(Item #5)</b> Create new shift for employeeId = 1</a></li>
			<li><a href="/api/shiftsByDate/2015-09-10 00:00:00/2015-09-17 00:00:00"><b>(Item #6)</b> View all shifts between start and end times</a></li>
			<li><a href="#" onclick="document.getElementById('item6').submit(); return false;"><b>(Item #7)</b> Update the times on shiftId = 12 to 2015-01-01 8-10AM</a></li>
			<li><a href="#" onclick="document.getElementById('item7a').submit(); return false;"><b>(Item #8)</b> Assign employeeId = 1 to a shiftId = 12</a></li>
			<li><a href="#" onclick="document.getElementById('item7b').submit(); return false;"><b>(Item #8)</b> Assign employeeId = 2 to a shiftId = 12</a></li>
			<li><a href="/api/employee/1"><b>(Item #9)</b> View details for employeeId = 1</a></li>
		</ul>
		<ul>
			<li><a href="/api/employee/4">View details for employeeId = 4 (NOT an employee)</a></li>
			<li><a href="/api/shifts/1/2015-09-10 00:00:00/2015-09-17 00:00:00">View assigned shifts for employeeId = 1 with startTime and endTime filtering</a></li>
			<li><a href="/api/shifts/4">View assigned shifts for managerId = 4</a></li>
			<li><a href="/api/shifts/4?employee">View assigned shifts for managerId = 4 with employee contact information</a></li>
		</ul>

		<form id="item5" action="/api/shift/create/1" method="post">
			<input type="hidden" name="managerId" value="5">
			<input type="hidden" name="break" value="0.5">
			<input type="hidden" name="startTime" value="2015-01-01 08:00:00">
			<input type="hidden" name="endTime" value="2015-01-01 10:00:00">
		</form>

		<form id="item6" action="/api/shift/12/update" method="post">
			<input type="hidden" name="startTime" value="2015-01-01 08:00:00">
			<input type="hidden" name="endTime" value="2015-01-01 10:00:00">
		</form>
		
		<form id="item7a" action="/api/shift/12/assign/1" method="post"></form>
		
		<form id="item7b" action="/api/shift/12/assign/2" method="post"></form>
	</body>
</html>