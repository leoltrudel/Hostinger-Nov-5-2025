<?php
ob_start();
include_once 'includes/header.inc.php';
$headerOutput = ob_get_contents();
ob_end_clean();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Function to format the date
function formatDate($date)
{
    $timestamp = strtotime($date);
    $todayDate = date('Y-m-d');
    $dateOnly = date('Y-m-d', $timestamp);

    return ($dateOnly === $todayDate) ? date('g:i a', $timestamp) : date('F j, Y', $timestamp);
}

// Set the default time zone to ensure consistency
date_default_timezone_set('America/New_York');

// Fetch all usernames and map them by user id
$usernameData = "SELECT id, username FROM user";
$usernameResult = mysqli_query($conn, $usernameData);
$usernames = [];

if ($usernameResult && mysqli_num_rows($usernameResult) > 0) {
    while ($row = mysqli_fetch_assoc($usernameResult)) {
        $usernames[$row['id']] = $row['username'];
    }
}
// Generate a CSRF token if not already set

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check CSRF token
    //echo implode(array_keys($_POST));
    //var_dump(hash_equals($_SESSION['token'], $_POST['token']));
    if (isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {



        // Initialize the variable with a default value
        //$techstackName = '';

        // Check if the decline or accept button was pressed and process the form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check CSRF token
            if (isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
                // Generate links
                $userLink = '<a href="profile.php?id=' . urlencode($userid) . '">' . htmlspecialchars($usernames[$userid]) . '</a>';
                $grpLink = '<a href="entity.php?entity=' . urlencode($grpID) . '">' . htmlspecialchars($grpName) . '</a>';

                // Update $returnSubjectText with the links
                $returnSubjectText = 'You are now connected to ' . $userLink . ' from ' . $grpLink . '!';

                $getVariables = "SELECT tech_id, techstack, usecase_id, usecase FROM inbox WHERE inbox_id = " . intval($_POST['inbox_id']);
                $getVariablesResult = mysqli_query($conn, $getVariables);

                // Initialize variables
                $techstackID = null;
                $techstackName = '';
                $usecaseID = null;
                $usecaseName = '';

                if (!empty($getVariablesResult) && mysqli_num_rows($getVariablesResult) > 0) {
                    while ($row = mysqli_fetch_assoc($getVariablesResult)) {
                        $techstackID = $row['tech_id'];
                        $techstackName = $row['techstack'];
                        $usecaseID = $row['usecase_id'];
                        $usecaseName = $row['usecase'];
                    }
                }

                // Generate techstack and usecase links
                $techstackLink = '<a href="techstack.php?techstack=' . urlencode($techstackID) . '">' . htmlspecialchars($techstackName) . '</a>';
                $usecaseLink = '<a href="usecase.php?id=' . urlencode($usecaseID) . '">' . htmlspecialchars($usecaseName) . '</a>';

                // Hyperlink the email address
                $emailLink = '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>';

                // Prepare the return message regardless of action
                $returnMessageText = '<strong><i>' . $userLink . '</i></strong> from <strong><i>' . $grpLink . '</i></strong> has accepted your invitation to connect on Techstack: <strong><i>' . $techstackLink . '</i></strong> for Use Case: <i><strong>' . $usecaseLink . '</i></strong>. ' . $usernames[$userid] . '\'s email is: ' . $emailLink . '.';
                $returnSubject = mysqli_real_escape_string($conn, $returnSubjectText);
                $returnMessage = mysqli_real_escape_string($conn, $returnMessageText);
            } else {
                echo "Invalid or expired token.";
            }
        }
        // echo 'accepted!!!';
        if (isset($_POST['decline'])) {
            $sql = "UPDATE inbox SET hidden_value = NULL, retained = NULL WHERE inbox_id = " . intval($_POST['inbox_id']);
            if (mysqli_query($conn, $sql)) {
                // unset($_SESSION['token']);
                //  header("Location: " . $_SERVER['REQUEST_URI']);
                // exit();
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } elseif (isset($_POST['accept'])) {
            $inboxOwnerId = intval($_POST['inbox_owner_id']);
            $sql = "UPDATE inbox SET hidden_value = NULL WHERE inbox_id = " . intval($_POST['inbox_id']);
            $sqlSend = "INSERT INTO inbox (created_at, sender_id, subject_contents, message_contents, inbox_owner_id, retained, unread, hidden_value, techstack, usecase) VALUES (NOW(), $userid, '$returnSubject', '$returnMessage', $inboxOwnerId, 1, 1, NULL, NULL, NULL)";

            if (mysqli_query($conn, $sql) && mysqli_query($conn, $sqlSend)) {
                // unset($_SESSION['token']);
                //   header("Location: " . $_SERVER['REQUEST_URI']);
                //   exit();
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
            //echo "You have accepted the request.";
        }
    }
}
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Inbox and Outbox Data</title>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    echo $headerOutput;

    // Inbox Queries
    $sqlInbox1 = "SELECT inbox_id, inbox.created_at, sender_id, fav_list_id, subject_contents, message_contents, inbox_owner_id, retained, hidden_value, tech_id, techstack, usecase_id, usecase, com_id, com_name
FROM inbox
INNER JOIN user
ON inbox.sender_id = user.id
INNER JOIN join_user_grp
ON user.id = join_user_grp.user_id
INNER JOIN company
ON join_user_grp.grp_id = company.com_id
WHERE inbox_owner_id = $userid AND retained = 1 AND fav_list_id IS NULL AND inbox_owner_id != sender_id";
    $result1 = mysqli_query($conn, $sqlInbox1);

    $inboxData = [];
    if ($result1 && mysqli_num_rows($result1) > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $inboxData[] = [
                'inbox_id' => $row['inbox_id'],
                'created_at' => formatDate($row['created_at']),
                'sender_id' => $usernames[$row['sender_id']],
                'subject_contents' => $row['subject_contents'],
                'message_contents' => $row['message_contents'],
                'inbox_owner_id' => $row['inbox_owner_id'],
                'hidden_value' => $row['hidden_value'],
                'tech_id' => $row['tech_id'],
                'techstack' => $row['techstack'],
                'usecase_id' => $row['usecase_id'],
                'usecase' => $row['usecase'],
                'grp_id' => $row['com_id'],
                'grp_name' => $row['com_name']
            ];
        }
    }

    // Sort the combined array by created_at in descending order
    usort($inboxData, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Outbox Queries
    $sqlOutbox1 = "SELECT inbox_id, inbox.created_at, sender_id, fav_list_id, subject_contents, message_contents, inbox_owner_id, retained, hidden_value, tech_id, techstack, usecase_id, usecase, com_id, com_name
FROM inbox
INNER JOIN user
ON inbox.inbox_owner_id = user.id
INNER JOIN join_user_grp
ON user.id = join_user_grp.user_id
INNER JOIN company
ON join_user_grp.grp_id = company.com_id
WHERE sender_id = $userid
AND retained = 1
AND fav_list_id IS NULL AND inbox_owner_id != sender_id";
    $result2 = mysqli_query($conn, $sqlOutbox1);

    $outboxData = [];

    if ($result2 && mysqli_num_rows($result2) > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            $outboxData[] = [
                'inbox_id' => $row['inbox_id'],
                'created_at' => formatDate($row['created_at']),
                'sender_id' => isset($usernames[$row['inbox_owner_id']]) ? $usernames[$row['inbox_owner_id']] : 'Unknown User',
                'subject_contents' => $row['subject_contents'],
                'message_contents' => $row['message_contents'],
                'inbox_owner_id' => $row['inbox_owner_id'],
                'hidden_value' => $row['hidden_value'],
                'tech_id' => $row['tech_id'],
                'techstack' => $row['techstack'],
                'usecase_id' => $row['usecase_id'],
                'usecase' => $row['usecase'],
                'grp_id' => $row['com_id'],
                'grp_name' => $row['com_name']
            ];
        }
    }

    // Sort the combined array by created_at in descending order
    usort($outboxData, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    ?>
    <div class="fill-remaining">
        <div class="notifications-color">
            <br>
            <div class="notifications-container">
                <div id="group_buttons">
                    <button id="inbox_button" class="active">Inbox</button>
                    <button id="outbox_button">Outbox</button>
                </div>

                <div class="container">
                    <div class="left-container">
                        <div id="inboxTable">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Created At</th>
                                        <th>From</th>
                                        <th>Group</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inboxData as $data) : ?>
                                        <?php if ($data['hidden_value'] == 1) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td><?php
                                                    $senderUsername = $data['sender_id']; // Fetch the username without htmlspecialchars first
                                                    $encodedUsername = htmlspecialchars($senderUsername, ENT_QUOTES, 'UTF-8'); // Convert to HTML-safe string for display
                                                    $senderLink = '<a href="profile.php?id=' . urlencode(array_search($senderUsername, $usernames)) . '">' . $encodedUsername . '</a>';
                                                    echo $senderLink;
                                                    ?>
                                                    <?php
                                                    $inboxOwnerId = array_search($data['sender_id'], $usernames);
                                                    ?>
                                                    <form method="post" id="actionForm" action="<?php $_SERVER['REQUEST_URI']; ?>">
                                                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                                                        <input type="hidden" name="inbox_id" value="<?php echo $data['inbox_id']; ?>">
                                                        <input type="hidden" name="inbox_owner_id" value="<?php echo $inboxOwnerId; ?>">
                                                        <!-- Pass techstackID and techstackName as hidden fields -->
                                                        <input type="hidden" name="techstack_id" value="<?php echo htmlspecialchars($techstackID); ?>">
                                                        <input type="hidden" name="techstack_name" value="<?php echo htmlspecialchars($techstackName); ?>">
                                                        <input type="submit" name="accept" id="accept" value="Accept"></input>
                                                        <button type="submit" name="decline" id="decline">Decline</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <?php
                                                    $groupID = intval($data['grp_id']);
                                                    $groupName = htmlspecialchars($data['grp_name']);
                                                    echo '<a href="entity.php?entity=' . $groupID . '">' . $groupName . '</a>';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars_decode($data['subject_contents']); ?></td>
                                                <td>
                                                    <?php
                                                    $techstackID = intval($data['tech_id']);
                                                    $techstackName = htmlspecialchars($data['techstack']);
                                                    $usecaseID = intval($data['usecase_id']);
                                                    $useCaseName = htmlspecialchars($data['usecase']);

                                                    $techstackLink = '<a href="techstack.php?techstack=' . $techstackID . '">' . $techstackName . '</a>';
                                                    $usecaseLink = '<a href="usecase.php?id=' . $usecaseID . '">' . $useCaseName . '</a>';

                                                    echo 'Hi! I would like to chat with you about Techstack: <strong><i>' . $techstackLink . '</i></strong>, as applied to Use Case: <strong><i>' . $usecaseLink . '</i></strong>.';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php else : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td>
                                                    <?php
                                                    /////
                                                    $senderUsername = $data['sender_id']; // Fetch the username without htmlspecialchars first
                                                    $encodedUsername = htmlspecialchars($senderUsername, ENT_QUOTES, 'UTF-8'); // Convert to HTML-safe string for display
                                                    $senderLink = '<a href="profile.php?id=' . urlencode(array_search($senderUsername, $usernames)) . '">' . $encodedUsername . '</a>';
                                                    echo $senderLink;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $groupID = intval($data['grp_id']);
                                                    $groupName = htmlspecialchars($data['grp_name']);
                                                    echo '<a href="entity.php?entity=' . $groupID . '">' . $groupName . '</a>';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars_decode($data['subject_contents']); ?></td>
                                                <td><?php echo htmlspecialchars_decode($data['message_contents']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>


                            <?php if (empty($inboxData)) {
                                echo "Your inbox is empty.";
                            } ?>
                        </div>

                        <div id="outboxTable">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Created At</th>
                                        <th>Sent To</th>
                                        <th>Group</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($outboxData as $data) : ?>
                                        <?php if ($data['hidden_value'] == 1) : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td><?php echo "<i>Hidden User</i>"; ?></td>
                                                <td><?php echo "<i>Hidden Group</i>"; ?></td>
                                                <!--  <td>
                                                    <?php /*
                                                    $groupID = intval($data['grp_id']);
                                                    $groupName = htmlspecialchars($data['grp_name']);
                                                    echo '<a href="entity.php?entity=' . $groupID . '">' . $groupName . '</a>'; */
                                                    ?> 
                                                </td>-->
                                                <td><?php echo htmlspecialchars_decode($data['subject_contents']); ?></td>
                                                <td>
                                                    <?php
                                                    // Generate the URLs for the links
                                                    $techstackLink = '<a href="techstack.php?techstack=' . urlencode($data['tech_id']) . '">' . htmlspecialchars($data['techstack']) . '</a>';
                                                    $usecaseLink = '<a href="usecase.php?id=' . urlencode($data['usecase_id']) . '">' . htmlspecialchars($data['usecase']) . '</a>';

                                                    // Output the message with the links
                                                    echo 'Hello! I would like to chat with you about Techstack: <strong><i>' . $techstackLink . '</i></strong>, as applied to Use Case: <strong><i>' . $usecaseLink . '</i></strong>.';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php else : ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td>
                                                    <?php
                                                    $senderUsername = $data['sender_id']; // Fetch the username without htmlspecialchars first
                                                    $encodedUsername = htmlspecialchars($senderUsername, ENT_QUOTES, 'UTF-8'); // Convert to HTML-safe string for display
                                                    $senderLink = '<a href="profile.php?id=' . urlencode(array_search($senderUsername, $usernames)) . '">' . $encodedUsername . '</a>';
                                                    echo $senderLink;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $groupID = intval($data['grp_id']);
                                                    $groupName = htmlspecialchars($data['grp_name']);
                                                    echo '<a href="entity.php?entity=' . $groupID . '">' . $groupName . '</a>';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars_decode($data['subject_contents']); ?></td>
                                                <td><?php echo htmlspecialchars_decode($data['message_contents']); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>


                            <?php if (empty($outboxData)) {
                                echo "Your outbox is empty.";
                            } ?>
                        </div>
                    </div>
                </div>

                <script>
                    function activateButton(groupSelector, buttonId) {
                        document.querySelectorAll(groupSelector + ' button').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        document.getElementById(buttonId).classList.add('active');
                    }

                    function showTable(tableId) {
                        document.querySelectorAll('div[id$="Table"]').forEach(function(table) {
                            table.style.display = 'none';
                        });
                        document.getElementById(tableId).style.display = 'block';
                    }

                    // Set up event listeners for the buttons
                    document.getElementById('inbox_button').addEventListener('click', function() {
                        activateButton('#group_buttons', 'inbox_button');
                        showTable('inboxTable');
                        localStorage.setItem('selectedTable', 'inboxTable');
                    });

                    document.getElementById('outbox_button').addEventListener('click', function() {
                        activateButton('#group_buttons', 'outbox_button');
                        showTable('outboxTable');
                        localStorage.setItem('selectedTable', 'outboxTable');
                    });

                    // On page load, apply the active state and show the correct table based on the last selection
                    document.addEventListener('DOMContentLoaded', function() {
                        if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                            const selectedTable = localStorage.getItem('selectedTable') || 'inboxTable';
                            const selectedButton = selectedTable === 'inboxTable' ? 'inbox_button' : 'outbox_button';

                            activateButton('#group_buttons', selectedButton);
                            showTable(selectedTable);
                        } else {
                            // Default to Inbox if the user navigates away and returns
                            activateButton('#group_buttons', 'inbox_button');
                            showTable('inboxTable');
                            localStorage.removeItem('selectedTable');
                        }
                    });
                </script>
            </div>
        </div>
    </div>

</body>

</html>