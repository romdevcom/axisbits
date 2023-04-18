<?php
require_once 'classes-task-1.php';

$bus = new Bus();
$bus->setSpeed(50);
echo $bus . "<br>";

$car = new Car();
$car->setSpeed(120);
echo $car . "<br>";

$bike = new Bike();
$bike->setSpeed(10);
echo $bike . "<br><br>";


require_once 'classes-task-2.php';

//to create Company 1
$company = new Company("Company 1");
//$company->table();
//$office = new Office("Office 1", $company->id);
//$office->table();
//$employee = new Employee("Person 11", $office->id, rand(500, 5000));
//$employee->table();

for($i = 1; $i <= 5; $i++){
    $office = new Office("Office " . $i, $company->id);
    for($j = 1; $j <= 3; $j++){
        $employee = new Employee("Person " . $i . $j, $office->id, rand(500, 5000));
    }
}

$officesBySalary = $office->getSalarySorted();
if(count($officesBySalary)){
    echo "<table style='border:solid;'>";
    foreach($officesBySalary as $item){
        echo "<tr>
                <td>" . $item['name'] . "</td>
                <td>" . $item['total_salary'] . "</td>
              </tr>";
    }
    echo "</table><br>";
}

$officesEmployees = $office->getEmployeeBetween(5,19,3,23);
if(count($officesEmployees)){
    echo "<table style='border:solid;'>";
    foreach($officesEmployees as $item){
        echo "<tr>
                <td>" . $item['name'] . "</td>
                <td>" . $item['employee_count'] . "</td>
              </tr>";
    }
    echo "</table><br>";
}