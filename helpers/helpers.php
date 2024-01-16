<?php

function getParticipations($conn) {
    $sql = "SELECT employees.name as employee_name, employees.email as employee_email, events.name as event_name, events.date as event_date, events.version as event_version, participations.id, participations.fee as participation_fee FROM participations 
            JOIN employees ON participations.employee_id = employees.id
            JOIN events ON participations.event_id = events.id";
    
    $employeeName = $_GET['employee_name'] ?? null;
    $eventName = $_GET['event_name'] ?? null;
    $eventDate = $_GET['event_date'] ?? null;

    if (!empty($employeeName) || !empty($eventName) || !empty($eventDate)) {
        $searchStr = "";
        
        if (!empty($employeeName)) {
            $searchStr .= "employees.name = '{$employeeName}'";
        }
    
        if (!empty($eventName)) {
            $param = "events.name = '{$eventName}'";

            $searchStr  .= $searchStr ? " AND {$param}" : "$param";
        }
    
        if (!empty($eventDate)) {
            $date = dateFormat($eventDate);
            $param = "events.date = '{$date}'";

            $searchStr  .= $searchStr ? " AND {$param}" : "$param";
        }
    }
    
    $sql .= $searchStr ? " WHERE {$searchStr}" : "";

    // var_dump($sql);
    // die();

    return mysqli_query($conn, $sql);
}

function showParticipations($conn) {
    $result = getParticipations($conn);
    $totalFee = 0;

    while ($participation = mysqli_fetch_assoc($result)) {
        $totalFee += $participation['participation_fee'];
        ?>
        <tr>
            <th class="text-center" scope="row"><?php echo $participation['id']; ?></th>
            <td class=""><?php echo $participation['employee_name']; ?></td>
            <td class="text-center"><?php echo $participation['event_name']; ?></td>
            <td class="text-center"><?php echo $participation['event_date']; ?></td>
            <td class="text-center"><?php echo $participation['event_version']; ?></td>
            <td class="text-right"><?php echo $participation['participation_fee']; ?></td>
        </tr>
        <?php
    }
        ?>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right" scope="row">Total Fee: </th>
                <td class="text-right"><?php echo number_format($totalFee, 2); ?></td>
            </tr>
        </tfoot>
        <?php
}

function storeEvents($conn, $filePath)
{
    if (!empty($filePath)) {
        $content = file_get_contents($filePath);
        $events = json_decode($content);
        
        foreach ($events as $event) {
            $employeeId = storeEmployee($conn, $event->employee_name, $event->employee_mail);
            $eventId = storeEvent($conn, $event->event_name, dateFormat($event->event_date), $event->version);
            storeParticipation($conn, $employeeId, $eventId, $event->participation_fee);
        }

        return true;
    }

    return false;
}

function storeEmployee($conn, $name, $email)
{
    if ($result = hasExists($conn, 'employees', 'email', $email)) {
        return $result;
    }

    $sql = "INSERT INTO employees (`name`, `email`) VALUES ('$name', '$email')";

    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    } else {
        return false;
    }
}

function storeEvent($conn, $name, $date, $version = null)
{
    if ($result = hasExists($conn, 'events', 'name', $name)) {
        return $result;
    }

    $sql = "INSERT INTO events (`name`, `date`, `version`) VALUES ('$name', '$date', '$version')";

    if (mysqli_query($conn, $sql)) {
       return mysqli_insert_id($conn);
    } else {
        return false;
    }
}

function storeParticipation($conn, $employeeId, $eventId, $fee)
{
    $sql = "INSERT INTO participations (`employee_id`, `event_id`, `fee`) VALUES ('$employeeId', '$eventId', '$fee')";

    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    } else {
        return false;
    }
}

function dateFormat($dateStr, $format = 'Y-m-d')
{
    return date($format, strtotime($dateStr));
}

function hasExists($conn, $tableName, $column, $value)
{
    $sql = "SELECT * FROM {$tableName} WHERE {$column} = '{$value}'";

    $result = mysqli_query($conn, $sql);

    if ($data = mysqli_fetch_row($result)) {
        return $data[0];
    }
    
    return false;
}