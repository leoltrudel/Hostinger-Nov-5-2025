<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Dropdown</title>
    <style>
        /* Basic styles for the browse link and dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            top: 4vh;
            min-width: auto;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>

    <div class="dropdown">
        <!-- The Browse link -->
        <a href="#" onclick="toggleDropdown()">Browse</a>

        <!-- The dropdown menu -->
        <div id="myDropdown" class="dropdown-content">
            <a href="#">Products</a>
            <a href="#">Techstacks</a>
            <a href="#">Use Cases</a>
            <a href="#">Utilities</a>
            <a href="#">Vendors</a>
        </div>
    </div>

    <script>
        // Function to toggle dropdown visibility
        function toggleDropdown() {
            var dropdown = document.getElementById("myDropdown");
            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }

        // Optional: Close dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown a')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
            }
        }
    </script>

</body>

</html>