<?php
    require_once 'helpers/helpers.php';
    require_once 'database/conn.php';

    $msg = $_GET['msg'] ?? null;
    $alert = $_GET['alert'] ?? 'success';
    $task = $_GET['task'] ?? 'report';

    $employeeName = $_GET['employee_name'] ?? '';
    $eventName = $_GET['event_name'] ?? '';
    $eventDate = $_GET['event_date'] ?? '';

    if ($_POST['submit'] == 'upload') {
        if (empty($_FILES['event_json']['tmp_name'])) {
            $alert = 'danger';
            $msg = 'The file upload field is required!';
        }
        else if ($_FILES['event_json']['type'] != 'application/json') {
            $alert = 'danger';
            $msg = 'The uploaded file in not a valid josn file!';
        }
        else {
            $result = storeEvents($conn, $_FILES['event_json']['tmp_name']);

            if ($result === true) {
                $alert = 'success';
                $msg = 'Successfully store events in database.';

                header("Location: index.php?task=report&alert={$alert}&msg={$msg}");
            } else {
                $alert = 'danger';
                $msg =  'Failed to store events!';
            }
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Code Challenge</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .container {
                width: 900px;
                margin-top: 100px;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="nav-section">
                <h2>Code Challenge</h2>
                <hr />
                <?php include_once 'nav.php'; ?>
            </div>

            <!-- alert message section -->
            <?php if ($msg): ?>
                <div class="alert alert-<?php echo $alert; ?> alert-dismissible mt-4">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <strong>
                        <?php if ('success' == $alert) { echo 'Success!'; } else if ('warning' == $alert) { echo 'Oops'; } else { echo 'Error!'; } ?>
                    </strong> 
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            
            <?php if ('report' == $task): ?>
                <div class="list-section my-3 px-2 py-2 border bg-light">
                    <form class="row g-3 form-inline" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="col-3">
                            <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee name" value="<?php echo $employeeName; ?>">
                        </div>

                        <div class="col-3">
                            <input type="text" class="form-control" id="event_name" name="event_name" placeholder="Event name" value="<?php echo $eventName; ?>">
                        </div>

                        <div class="col-3">
                            <input type="date" class="form-control" id="event_date" name="event_date" placeholder="Event date" value="<?php echo $eventDate; ?>">
                        </div>

                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-warning">Reset</a>
                        </div>
                    </form>
                </div>
            
                <div class="list-section my-4 bg-light">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Event Name</th>
                                <th scope="col">Event Date</th>
                                <th scope="col">Version</th>
                                <th scope="col">Fee</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php showParticipations($conn); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ('read' == $task): ?>
                <div class="list-section my-4 px-5 py-4 border bg-light">
                    <form class="row g-3" method="post" enctype="multipart/form-data">
                        <div class="col-12">
                            <label for="event_json" class="form-label">JSON File</label>
                            <input type="file" class="form-control" id="event_json" name="event_json" required accept="application/json">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" name="submit" value="upload">SUBMIT</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?> 
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>