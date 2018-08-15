// Home page

// Filters

function filterPosts() {
    scope = document.getElementById("scope").value
    limit = document.getElementById("limit").value
    order = document.getElementById("order").value

    url = url_home + "/filter/" + scope + "/" + limit + "/" + order

    window.location = url
}

/**
 * NOTE: There's a bug in this function, if you discovered the origin of the bug, please inform us (@see commented lines at the end)
 */
function recordAsDefault() {
    url_scope = url_scope.replace("aaa", document.getElementById("scope").value)

    url_limit = url_limit.replace("aaa", document.getElementById("limit").value)

    url_order = url_order.replace("aaa", document.getElementById("order").value)

    success = false

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText === "true") {
                success = true
            } else {
                success = false
            }
        }
    };
    xmlhttp.open("GET", url_scope, true);
    xmlhttp.send();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText === "true") {
                success = true
            } else {
                success = false
            }
        }
    };
    xmlhttp.open("GET", url_limit, true);
    xmlhttp.send();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText === "true") {
                success = true
            } else {
                success = false
            }
        }
    };
    xmlhttp.open("GET", url_order, true);
    xmlhttp.send();

    //if (success === true) {
        document.getElementById("filterReady").style.display = "block";
    //} else {
    //    document.getElementById("filterError").style.display = "block";
    //}
}

// Fetching posts

/**
 * Getting the number of new posts
 */
function getNbNewPosts() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Inserting the response text inside the #nbNewPosts Element.
            document.getElementById("nbNewPosts").innerHTML = this.responseText;

            // Deciding if the .newPosts Element is displayed.
            if (Number(this.responseText) > 0) {
                document.getElementsByClassName("newPosts")[0].style.display = "flex";
            } else {
                document.getElementsByClassName("newPosts")[0].style.display = "none";
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

/**
 * Inserting the new posts and modifying the urls
 */
function getNewPosts() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // First of all we fetch the new posts.
            newPosts = this.responseText;

            // Then we split the result to retrieve the new $last_id.
            splited_posts = newPosts.split('/');

            // We change the old $last_id by the new one:
            // 1. We split the url with a '/' as seperator.
            splited_url = url.split('/');
            // 2. We select the $last_id index.
            i = splited_url.indexOf("post") + 1;
            // 3. We replace the old $last_id.
            splited_url[i] = splited_posts[0];
            // 4. And we join everything together to have a new URL with the new $last_id.
            url = splited_url.join('/');

            // We make exactly the same process but this time with the render URL.
            splited_url = url_r.split('/');
            i = splited_url.indexOf("post") + 1;
            splited_url[i] = splited_posts[0];
            url_r = splited_url.join('/');

            // We remove the $last_id that was sended with the response.
            splited_posts.splice(0, 1);

            // And we join everything together to get only the HTML code.
            newPosts = splited_posts.join('/');

            // And we insert it before the first .text Element.
            document.getElementsByClassName("text")[0].insertAdjacentHTML('beforebegin', newPosts);

            // And we refresh the number of new posts.
            getNbNewPosts();
        }
    };
    xmlhttp.open("GET", url_r, true);
    xmlhttp.send();
}

// Report

function openReportWindow() {
    var xmlhttp = new XMLHttpRequest()
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.body.style.overflowY = "hidden"
            document.body.innerHTML += this.responseText
        }
    }
    xmlhttp.open("GET", url_report, true);
    xmlhttp.send();
}

function closeReportWindow() {
    document.body.style.overflowY = "auto"
    reports = document.getElementsByClassName('reportBackground')

    length = reports.length

    for (var i = 0; i < length; i++) {
        reports[0].parentNode.removeChild(reports[0])
    }
}
