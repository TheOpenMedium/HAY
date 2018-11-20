// Home page

// Filters

function filterPosts() {
    scope = document.getElementById("scope").value
    limit = document.getElementById("limit").value
    order = document.getElementById("order").value

    url_filter = url_home + "/filter/" + scope + "/" + limit + "/" + order

    window.location = url_filter
}

function recordAsDefault() {
    url_scope = url_scope.replace("aaa", document.getElementById("scope").value)
    url_limit = url_limit.replace("aaa", document.getElementById("limit").value)
    url_order = url_order.replace("aaa", document.getElementById("order").value)

    var success = true
    var order = 0

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            order++
            if (this.responseText !== "true" && success === true) {
                success = false
                document.getElementById("filterError").style.display = "block"
            }
            if (order === 3 && success === true) {
                document.getElementById("filterReady").style.display = "block"
            }
        }
    };
    xmlhttp.open("GET", url_scope, true);
    xmlhttp.send();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            order++
            if (this.responseText !== "true" && success === true) {
                success = false
                document.getElementById("filterError").style.display = "block"
            }
            if (order === 3 && success === true) {
                document.getElementById("filterReady").style.display = "block"
            }
        }
    };
    xmlhttp.open("GET", url_limit, true);
    xmlhttp.send();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            order++
            if (this.responseText !== "true" && success === true) {
                success = false
                document.getElementById("filterError").style.display = "block"
            }
            if (order === 3 && success === true) {
                document.getElementById("filterReady").style.display = "block"
            }
        }
    };
    xmlhttp.open("GET", url_order, true);
    xmlhttp.send();
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

            // And we evaluate all script tags.
            evalScript()

            // And Highlight.js do some highlighting
            highlight()
        }
    };
    xmlhttp.open("GET", url_r, true);
    xmlhttp.send();
}

// Surveys

/**
 * Sending surveys
 */
function sendSurvey(id, e) {
    // TODO: Adding a security if a survey appear multiple times in a page
    document.getElementById('surveySuccess' + id).style.display = "none"
    document.getElementById('surveyError' + id).style.display = "none"

    var url_survey_temp = url_survey.replace('aaa', id)

    try {
        var answer = document.querySelector('input[name="surveyOption' + id + '"]:checked').value
    } catch (e) {
        document.getElementById('surveyError' + id).style.display = "inline"
        return;
    }

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == "true") {
                document.getElementById('surveySuccess' + id).style.display = "inline"
                setTimeout(refreshSurvey, 5000, id, e);
            } else {
                document.getElementById('surveyError' + id).style.display = "inline"
                console.log(this.responseText)
                setTimeout(refreshSurvey, 5000, id, e);
            }
        }
    };
    xmlhttp.onerror = function() {
        document.getElementById('surveyError' + id).style.display = "inline"
        refreshSurvey(id, e);
    };
    xmlhttp.open("POST", url_survey_temp, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send("answer=" + encodeURIComponent(answer));
}

/**
 * Refreshing survey
 */
function refreshSurvey(id, e) {
    var url_survey_temp = url_survey_refresh.replace('aaa', id)

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            while (e.id !== "survey_" + id) {
                e = e.parentNode
            }
            e.outerHTML = this.responseText

            re = new RegExp("^\\s+$")

            document.getElementsByClassName("results")[0].childNodes.forEach(function (elt) {if (re.test(elt.data)) {elt.parentNode.removeChild(elt);}})
            document.getElementsByClassName("resultsValues")[0].childNodes.forEach(function (elt) {if (re.test(elt.data)) {elt.parentNode.removeChild(elt);}})
        }
    };
    xmlhttp.open("GET", url_survey_temp, true);
    xmlhttp.send();
}

/**
 * Fetching a Survey
 */
function getSurvey(id, e) {
    var url_survey_temp = url_survey_refresh.replace('aaa', id)

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            elt = document.getElementById(''+e)
            elt.outerHTML = this.responseText

            // Removing empty nodes
            re = new RegExp("^\\s+$")

            Array.from(document.getElementsByClassName("results")).forEach(function (elt) {elt.childNodes.forEach(function (elt) {if (re.test(elt.data)) {elt.parentNode.removeChild(elt)}})})
            Array.from(document.getElementsByClassName("resultsValues")).forEach(function (elt) {elt.childNodes.forEach(function (elt) {if (re.test(elt.data)) {elt.parentNode.removeChild(elt)}})})
        }
    };
    xmlhttp.open("GET", url_survey_temp, true);
    xmlhttp.send();
}

/**
 * Evaluate Script tags in posts
 */
function evalScript() {
    var matches = document.querySelectorAll(".markdown script")

    for (var i = 0; i < matches.length; i++) {
        eval(matches[i].innerHTML)
    }
}

/**
 * Reload Highlight.js
 */
function highlight() {
    var matches = document.querySelectorAll(".markdown pre > code:not(.hljs)")

    for (var i = 0; i < matches.length; i++) {
        hljs.highlightBlock(matches[i])
    }
}
