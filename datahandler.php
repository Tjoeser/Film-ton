<?php


function setupDatahandlerConnection()
{
    if (LIVE_MODE) {
        $host = host_live;
        $dbdriver = dbdriver_live;
        $dbname = dbname_live;
        $username = username_live;
        $password = password_live;
    } else {
        $host = host;
        $dbdriver = dbdriver;
        $dbname = dbname;
        $username = username;
        $password = password;
    }
    


    try {
        $dbh = new PDO("$dbdriver:host=$host;dbname=$dbname", $username, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh; // Return the PDO instance
    } catch (PDOException $e) {
        echo "Connection with " . $dbdriver . " failed: " . $e->getMessage();
        return null; // Return null if connection fails
    }
}

function setupAccountTableConnection()
{
    // Use the setupDatahandlerConnection to get the PDO instance
    $dbh = setupDatahandlerConnection();

    if ($dbh) {
        try {
            // Check if the connection is established and 'accounts' table is accessible
            $query = "SHOW TABLES LIKE 'accounts'";
            $stmt = $dbh->query($query);
            $result = $stmt->fetch();

            if ($result) {
                // echo true;
                return $dbh; // Return the PDO instance if 'accounts' table exists
            } else {
                echo "Table 'accounts' does not exist.";
                return null; // Return null if the table does not exist
            }
        } catch (PDOException $e) {
            echo "Error checking the 'accounts' table: " . $e->getMessage();
            return null;
        }
    }

    return null; // Return null if connection setup fails
}

function setupWatchlistTableConnection()
{
    // Use the setupDatahandlerConnection to get the PDO instance
    $dbh = setupDatahandlerConnection();

    if ($dbh) {
        try {
            // Check if the connection is established and 'watchlist' table is accessible
            $query = "SHOW TABLES LIKE 'watchlist'";
            $stmt = $dbh->query($query);
            $result = $stmt->fetch();

            if ($result) {
                // echo "Table 'watchlist' does exist.";
                return $dbh; // Return the PDO instance if 'watchlist' table exists
            } else {
                // echo "Table 'watchlist' does not exist.";
                return null; // Return null if the table does not exist
            }
        } catch (PDOException $e) {
            echo "Error checking the 'watchlist' table: " . $e->getMessage();
            return null;
        }
    }

    return null; // Return null if connection setup fails
}


function registerAccountDatahandler($email, $password, $countryCode)
{
    // Establish a connection to the database
    $dbh = setupAccountTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to insert data into the 'accounts' table
            $sql = "INSERT INTO accounts (email, password, countrycode) VALUES (:email, :password, :countryCode)";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':countryCode', $countryCode);

            // Execute the statement
            $stmt->execute();

            // Get the last inserted account ID
            $accountId = $dbh->lastInsertId();

            // Call the registerWatchlistDatahandler to create a new entry in the watchlist
            $watchlistId = registerWatchlistDatahandler($accountId);

            if ($watchlistId) {
                // Optionally, update the accounts table with the watchlist ID if needed
            } else {
                echo "Account registered, but failed to create a watchlist entry.";
            }
        } catch (PDOException $e) {
            echo "Error registering account: " . $e->getMessage();
        }
    } else {
        echo "Failed to connect to the database.";
    }
}

function loginAccountDatahandler($email, $password)
{
    // Establish a connection to the database
    $dbh = setupAccountTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to select the account based on email and password
            $sql = "SELECT * FROM accounts WHERE email = :email AND password = :password";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            // Execute the statement
            $stmt->execute();

            // Fetch the account data
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account) {
                // Account found, set the user ID cookie
                $userId = $account['accountId']; // Assuming 'id' is the column name for the user ID
                setcookie('user_id', $userId, time() + (86400 * 30), "/"); // 30 days expiry

                // Return the account data as an associative array
                return $account;
            } else {
                // No account found
                echo "Invalid email or password.";
                return null; // Return null if no account matches
            }
        } catch (PDOException $e) {
            echo "Error logging in: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}

function registerWatchlistDatahandler($accountId)
{
    // Establish a connection to the watchlist table
    $dbh = setupWatchlistTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to insert data into the 'watchlist' table
            $sql = "INSERT INTO watchlist (accountId) VALUES (:accountId)";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':accountId', $accountId);

            // Execute the statement
            $stmt->execute();

            // Return the last inserted ID
            return $dbh->lastInsertId();
        } catch (PDOException $e) {
            echo "Error registering watchlist entry: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}



function pullWatchlistDatahandler($accountId)
{
    // Establish a connection to the watchlist table
    $dbh = setupWatchlistTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to select entries from the 'watchlist' table
            $sql = "SELECT * FROM watchlist WHERE accountId = :accountId";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':accountId', $accountId);

            // Execute the statement
            $stmt->execute();

            // Fetch all matching records
            $watchlistEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // var_dump($watchlistEntries);
            return $watchlistEntries; // Return the fetched data
        } catch (PDOException $e) {
            echo "Error retrieving watchlist entries: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}
function addToWatchlistDatahandler($userId, $movieId)
{
    // Establish a connection to the watchlist table
    $dbh = setupWatchlistTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to insert a new entry into the 'watchlist' table
            $sql = "INSERT INTO watchlist (accountId, movieid) VALUES (:accountId, :movieId)";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':accountId', $userId); // Bind userId to accountId
            $stmt->bindParam(':movieId', $movieId); // Bind movieId

            // Execute the statement
            $stmt->execute();

            // Return the last inserted ID
            return $dbh->lastInsertId();
        } catch (PDOException $e) {
            echo "Error adding to watchlist: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}

function removeFromWatchlistDatahandler($userId, $movieId)
{
    // Establish a connection to the watchlist table
    $dbh = setupWatchlistTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to delete an entry from the 'watchlist' table
            $sql = "DELETE FROM watchlist WHERE accountId = :accountId AND movieid = :movieId";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':accountId', $userId); // Bind userId to accountId
            $stmt->bindParam(':movieId', $movieId); // Bind movieId

            // Execute the statement
            $stmt->execute();

            // Check how many rows were affected (deleted)
            if ($stmt->rowCount() > 0) {
                return true; // Successfully removed
            } else {
                return false; // No rows affected (entry not found)
            }
        } catch (PDOException $e) {
            echo "Error removing from watchlist: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}

function isOnWatchlistDatahandler($userId, $movieId)
{
    // Retrieve the watchlist data for the user
    $watchlistData = pullWatchlistDatahandler($userId);

    // Check if watchlist data is an array
    if (is_array($watchlistData)) {
        foreach ($watchlistData as $entry) {
            // Check if the movieId matches any entry in the watchlist
            if (isset($entry['movieid']) && $entry['movieid'] == $movieId) {
                return true; // Movie is in the watchlist
            }
        }
    }

    return false; // Movie is not in the watchlist
}

function pullSpecificAccountDataDatahandler($dataToPull)
{
    if (isset($_COOKIE['user_id'])) {
        $userId = htmlspecialchars(trim($_COOKIE['user_id'])); // Retrieve and sanitize the cookie value
    } else {
        $userId = null; // Or assign a default value if the cookie doesn't exist
    }
    $data = "";

    // Set the column to pull based on the dataToPull parameter
    switch ($dataToPull) {
        case "id":
            $data = "accountId";
            break;
        case "email":
            $data = "email";
            break;
        case "password":
            $data = "password";
            break;
        case "countrycode":
            $data = "countrycode";
            break;
        case "streaming":
            $data = "streaming";
            break;
        default:
            return null; // Handle invalid dataToPull input
    }

    // Establish a connection to the database
    $dbh = setupAccountTableConnection();

    if ($dbh) {
        try {
            // Prepare the SQL query to select the account based on userId
            $sql = "SELECT $data FROM accounts WHERE accountId = :id";
            $stmt = $dbh->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':id', $userId);

            // Execute the statement
            $stmt->execute();

            // Fetch the account data
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if $res is false before accessing the array
            if ($res && isset($res[$data])) {
                return $res[$data];
            } else {
                // Handle case where no account is found
                return null;
            }
        } catch (PDOException $e) {
            echo "Error fetching account data: " . $e->getMessage();
            return null; // Return null on error
        }
    } else {
        echo "Failed to connect to the database.";
        return null; // Return null if the connection fails
    }
}


function addStreamingServicesDatahandler($services)
{
    if (isset($_COOKIE['user_id'])) {
        $userId = htmlspecialchars(trim($_COOKIE['user_id'])); // Retrieve and sanitize the cookie value
    } else {
        $userId = null; // Handle case where cookie doesn't exist
    }

    if (!$userId) {
        echo "User ID not found.";
        return;
    }

    $dbh = setupAccountTableConnection();

    if ($dbh) {
        try {
            // Ensure $services is an array
            if (!is_array($services)) {
                $services = [$services]; // Wrap single value in an array
            }

            // Encode the new services to JSON
            $servicesJson = json_encode($services);

            // Update the database with the new services list (overriding the existing ones)
            $sql = "UPDATE accounts SET streaming = :services WHERE accountId = :id";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':services', $servicesJson);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            echo "Streaming services successfully updated!";
        } catch (PDOException $e) {
            echo "Error updating streaming services: " . $e->getMessage();
        }
    } else {
        echo "Failed to connect to the database.";
    }
}


