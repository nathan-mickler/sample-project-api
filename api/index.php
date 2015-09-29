<?php

require __DIR__ . '/../vendor/autoload.php';

include __DIR__ . '/database.php';

use SampleProject\Auth;

date_default_timezone_set('America/Chicago');
session_start();

$app = Spark\Application::boot();

$app->setMiddleware([
    'Relay\Middleware\ResponseSender',
    'Spark\Handler\ExceptionHandler',
    'Spark\Handler\RouteHandler',
    'Spark\Handler\ActionHandler',
]);

$app->addRoutes(function(Spark\Router $r) {
    // helper function just to initalize database
    $r->get('/api/initialize', 'SampleProject\Domain\InitializeDatabase');

    // helper function to assign active role (employee or manager)
    $r->get('/api/login/{role}', 'SampleProject\Domain\AssignRole');

    // list shifts (handles employee list, manager list, and employee list with managers)
    // to display an employee list with managers, simply append ?manager
    $r->get('/api/shifts/{userId}[/{startTime}[/{endTime}]]', 'SampleProject\Domain\ListShifts');

    // list other employees with any overlapping shifts
    $r->get('/api/coworkers/{employeeId}/{shiftId}', 'SampleProject\Domain\ListCoworkers');

    // generate a weekly summary of employee's time worked
    $r->get('/api/summary/{employeeId}', 'SampleProject\Domain\EmployeeSummary');

    // These routes are only available to managers
    if(Auth::isManager()) {
        // list all shifts by date
        $r->get('/api/shiftsByDate/{startTime}/{endTime}', 'SampleProject\Domain\ListShiftsByDate');

        // create new shift (post defined for ease of testing/demonstration)
        $r->put('/api/shift/create/{employeeId}', 'SampleProject\Domain\CreateShift');
        $r->post('/api/shift/create/{employeeId}', 'SampleProject\Domain\CreateShift');

        // make update times for a shift (post defined for ease of testing/demonstration)
        $r->put('/api/shift/{shiftId}/update', 'SampleProject\Domain\UpdateShift');
        $r->post('/api/shift/{shiftId}/update', 'SampleProject\Domain\UpdateShift');

        // update employee working a shift (post defined for ease of testing/demonstration)
        $r->put('/api/shift/{shiftId}/assign/{employeeId}', 'SampleProject\Domain\AssignShift');
        $r->post('/api/shift/{shiftId}/assign/{employeeId}', 'SampleProject\Domain\AssignShift');

        // see employee details
        $r->get('/api/employee/{employeeId}', 'SampleProject\Domain\EmployeeDetails');
    }
});

$app->run();