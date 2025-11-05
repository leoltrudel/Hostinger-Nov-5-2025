<!DOCTYPE html>
<html>

<head>
    <title>User Profile and Tech Stacks</title>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    require_once 'includes/dbh.inc.php';
    require_once 'includes/header.inc.php';
    require_once 'includes/session.inc.php';

    $profileID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT id, username, userUid, com_name, com_id, email, headshot
FROM user 
INNER JOIN join_user_grp
ON user.id = join_user_grp.user_id
INNER JOIN company
ON join_user_grp.grp_id = company.com_id
WHERE id = " . htmlspecialchars($profileID);
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['username'];
            $userUid = $row['userUid'];
            $comID = $row['com_id'];
            $company = $row['com_name'];
            $email = $row['email'];
            $headshot = $row['headshot'];
        }
    }

    $userImage = '';

    $userContent = '';

    if (!empty($headshot)) {
        $encodedHeadshot = base64_encode($headshot);
        $userImage .= '<a><img class="profile-image" src="data:image/png;base64,' . $encodedHeadshot . '" style="max-width: 10%; height: auto; display: block; margin-bottom: 1em;"></a>';
    } else {
        $userImage .= '<i>(No image available).</i>';
    }

    $userContent .= '<strong>User\'s Name</strong><br>';
    $userContent .= htmlspecialchars($name) . '<br><br>';

    $userContent .= '<strong>Username</strong><br>';
    $userContent .= htmlspecialchars($userUid) . '<br><br>';

    $userContent .= '<strong>Company</strong><br>';
    if (!$company) {
        $userContent .= '<i>(No company listed).</i><br><br>';
    } else {
        $userContent .= '<a href="entity.php?entity=' . urlencode($comID) . '">' . htmlspecialchars($company) . '</a><br><br>';
    }

    $userContent .= '<strong>Email</strong><br>';
    $userContent .= '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a><br><br>';


    $sql = "SELECT use_case.uc_id, use_case.use_case_name, techstack_name.tech_id, techstack_name.techstack_name, user.id
FROM use_case
INNER JOIN join_use_case_techstack
ON use_case.uc_id = join_use_case_techstack.use_case_id
INNER JOIN techstack_name
ON join_use_case_techstack.techstack_id = techstack_name.tech_id
INNER JOIN dummy_data
ON techstack_name.tech_id = dummy_data.tech_id
INNER JOIN user
ON dummy_data.user_id = user.id
WHERE id = $userid";
    $result = mysqli_query($conn, $sql);

    $techstackData = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $techstackName = $row['techstack_name'];
            $ucName = $row['use_case_name'];
            $ucID = $row['uc_id'];
            $techID = $row['tech_id'];

            if (!isset($techstackData[$techstackName])) {
                $techstackData[$techstackName] = [];
            }
            $techstackData[$techstackName][] = ['ucName' => $ucName, 'ucID' => $ucID, 'techID' => $techID];
        }
    }
    ?>

    <div class="container notifications-color">
        <div class="notifications-container">
            <div>
                <?php echo $userImage; ?>
            </div>
            <!-- Main content section -->
            <div class="user-content">
                <div class="main-content">
                    <?php echo $userContent; ?>
                </div>

                <!-- Conditionally render the table or output a message -->
                <?php if (!empty($techstackData)) : ?>
                    <div class="table-container table-default">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tech Stack Name</th>
                                    <th>Use Case Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($techstackData as $techstackName => $ucEntries) :
                                    $rowspan = count($ucEntries);
                                    foreach ($ucEntries as $index => $entry) : ?>
                                        <tr>
                                            <?php if ($index === 0) : ?>
                                                <td rowspan="<?php echo $rowspan; ?>">
                                                    <a href="techstack_details.php?id=<?php echo urlencode($entry['techID']); ?>">
                                                        <?php echo htmlspecialchars($techstackName); ?>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <a href="use_case_details.php?id=<?php echo urlencode($entry['ucID']); ?>">
                                                    <?php echo htmlspecialchars($entry['ucName']); ?>
                                                </a>
                                            </td>
                                        </tr>
                                <?php endforeach;
                                endforeach; ?>
                            </tbody>
                        </table>
                        <br>
                        <p> <?php echo '<a href="submitinfo.php">Add More Solutions</a>'; ?></p>
                    </div>
                <?php else : ?>
                    <div class="table-container">
                        <p>You have not added any knowledge. <?php echo '<a href="submitinfo.php">Add New Solutions</a>'; ?></p>
                    </div>
            </div>
        <?php endif; ?>
        </div>
    </div>
</body>

</html>