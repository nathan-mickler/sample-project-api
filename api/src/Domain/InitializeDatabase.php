<?php

namespace SampleProject\Domain;

use Spark\Adr\DomainInterface;
use Spark\Payload;
use SampleProject\Domain\Database\Shift;
use SampleProject\Domain\Database\Manager;
use SampleProject\Domain\Database\Employee;
use Illuminate\Database\Capsule\Manager as DatabaseManager;
use Carbon\Carbon;
use SampleProject\Payload\SuccessPayload;
use SampleProject\Payload\ErrorPayload;
use SampleProject\Payload\InvalidPayload;

class InitializeDatabase implements DomainInterface
{
    public function __invoke(array $input)
    {
		// down
		DatabaseManager::schema()->dropIfExists('users');
        DatabaseManager::schema()->dropIfExists('shifts');

        // up
        DatabaseManager::schema()->create('users', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('role', array('employee', 'manager'));
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });

        // up
        DatabaseManager::schema()->create('shifts', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('set null');
            $table->decimal('break', 5, 2);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
        });

        // seed database
        
        // create employees
        $Employees = [];
        $Employees[] = Employee::create([
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
            'phone' => '123-123-1234'
        ]);

        $Employees[] = Employee::create([
            'name' => 'Bob Jones',
            'email' => 'bob@gmail.com',
            'phone' => '234-234-1234'
        ]);

        $Employees[] = Employee::create([
            'name' => 'Jill Thompson',
            'email' => 'jill@gmail.com',
            'phone' => '345-345-1234'
        ]);

        // create managers
        $Managers = [];
        $Managers[] = Manager::create([
            'name' => 'Nathan Mickler',
            'email' => 'nathan@gmail.com',
            'phone' => '456-456-4567'
        ]);

        $Managers[] = Manager::create([
            'name' => 'Elizabeth Jackson',
            'email' => 'elizabeth@gmail.com',
            'phone' => '567-567-4567'
        ]);


        // create four overlapping shifts
        $Date = Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-05 00:00:00');
        $Shift = Shift::create([
            'break' => 1,
            'start_time' => $Date->copy()->addHours(8)->format('Y-m-d H:i:s'),
            'end_time' => $Date->copy()->addHours(17)->format('Y-m-d H:i:s')
        ]);

        // assign manager
        $Shift->assign($Managers[0]);
        $Shift->assign($Employees[0]);

        // shift #2
        $Date = Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-05 00:00:00');
        $Shift = Shift::create([
            'break' => 1,
            'start_time' => $Date->copy()->addHours(5)->format('Y-m-d H:i:s'),
            'end_time' => $Date->copy()->addHours(9)->format('Y-m-d H:i:s')
        ]);

        // assign manager
        $Shift->assign($Managers[0]);
        $Shift->assign($Employees[1]);

        // shift #3
        $Date = Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-05 00:00:00');
        $Shift = Shift::create([
            'break' => 1,
            'start_time' => $Date->copy()->addHours(12)->format('Y-m-d H:i:s'),
            'end_time' => $Date->copy()->addHours(19)->format('Y-m-d H:i:s')
        ]);

        // assign manager
        $Shift->assign($Managers[0]);
        $Shift->assign($Employees[1]);

        // shift #4
        $Date = Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-05 00:00:00');
        $Shift = Shift::create([
            'break' => 1,
            'start_time' => $Date->copy()->addHours(10)->format('Y-m-d H:i:s'),
            'end_time' => $Date->copy()->addHours(15)->format('Y-m-d H:i:s')
        ]);

        // assign manager
        $Shift->assign($Managers[0]);
        $Shift->assign($Employees[2]);

        // create random shifts (with random employees and managers)
        $Date = Carbon::createFromFormat('Y-m-d H:i:s', '2015-09-07 00:00:00');
        for($days = 0; $days < 28; $days++) {
            $Shift = Shift::create([
                'break' => 1,
                'start_time' => $Date->copy()->addDays($days)->addHours(8)->format('Y-m-d H:i:s'),
                'end_time' => $Date->copy()->addDays($days)->addHours(17)->format('Y-m-d H:i:s')
            ]);

            // assign manager
            $Shift->assign($Managers[rand(0, 1)]);
            $Shift->assign($Employees[rand(0, 2)]);
        }

        return SuccessPayload::create(['message' => 'Database Initialized']);
    }
}
