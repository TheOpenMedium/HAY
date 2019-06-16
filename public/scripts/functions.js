// Home page

// Post submit area

function changeColor(e) {
    color = "c" + e['target'].value
    textarea = document.getElementsByClassName('textarea')
    classes = textarea[0].className
    classes = classes.split("c")
    classes[0] += color
    textarea[0].className = classes[0]
}

function changeSize(e) {
    size = "fs" + e['target'].value
    textarea = document.getElementsByClassName('ta')
    classes = textarea[0].className
    classes = classes.split("fs")
    classes[0] += size
    textarea[0].className = classes[0]

    sizeDisplay = document.getElementsByClassName('sizeDisplay')
    classesSize = sizeDisplay[0].className
    classesSize = classesSize.split("fs")
    classesSize[0] += size
    sizeDisplay[0].className = classesSize[0]
}

// Edit post page

function init() {
    textarea = document.getElementsByClassName('textarea')
    classes = textarea[0].className
    classes = classes.split("c")
    classes[0] += color
    textarea[0].className = classes[0]

    size = "fs" + twig_size
    textarea = document.getElementsByClassName('ta')
    classes = textarea[0].className
    classes = classes.split("fs")
    classes[0] += size
    textarea[0].className = classes[0]

    sizeDisplay = document.getElementsByClassName('sizeDisplay')
    classesSize = sizeDisplay[0].className
    classesSize = classesSize.split("fs")
    classesSize[0] += size
    sizeDisplay[0].className = classesSize[0]

    sizeValue = document.getElementsByName('post[size]')
    sizeValue[0].value = twig_size
}

// Messages

function closeMsg(msgButton) {
    var messageButton = document.getElementById(msgButton)
    messageButton.parentNode.style.display = "none"
}

// Laws

function showLaws(e, id) {
    markdown = document.getElementById(id)

    if (markdown.style.display == "none") {
        markdown.style.display = "block"
        e.className = "icofont-thin-right icofont-rotate-90"
    } else {
        markdown.style.display = "none"
        e.className = "icofont-thin-right"
    }
}

// Markdown

function renderingMarkdown() {
    param = "markdown=" + encodeURIComponent(document.getElementById('laws_content').value)

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('preview').innerHTML = this.responseText
            evalScript()
        }
    };
    xmlhttp.open("POST", url_markdown, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(param);
}

// Tabs

function openTab(tabName) {
    tabcontent = document.getElementsByClassName("tabContent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    document.getElementById(tabName).style.display = "grid";
}

function openSubTab(subTabName) {
    tabcontent = document.getElementsByClassName("subTabContent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    document.getElementById(subTabName).style.display = "block";
}

// Report

function openReportWindow(entity, id) {
    url_report_temp = url_report.replace('aaa', entity)
    url_report_temp = url_report_temp.replace('bbb', id)

    var xmlhttp = new XMLHttpRequest()
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.body.style.overflowY = "hidden"
            document.body.innerHTML += this.responseText
        }
    }
    xmlhttp.open("GET", url_report_temp, true);
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

function changeReportLaws() {
    v = document.getElementById('reportLaw').value
    document.getElementById('form_law').value = v
}

/**
 * Render User tags
 */
function userTagRender() {
    $(".usertag_unprocessed").each(function(i) {
        var node = this;
        $.ajax({
                method: "POST",
                url: url_user_tag,
                data: {
                    tag: this.innerHTML
                }
            })
            .done(function (msg) {
                msg = JSON.parse(msg);
                html = '<a class="usertag" href="' + msg.url + '">' + msg.user.first_name + ' ' + msg.user.last_name
                html += '<div class="usertag_dropdown"><img src="' + msg.user.image + '" height="150px" /><span>' + msg.user.first_name + ' ' + msg.user.last_name + '</span><span>#' + msg.user.id + '</span>'
                if (msg.user.username != null) {
                    html += '<span>@' + msg.user.username + '</span>'
                }
                html += '</div></a>';
                node.outerHTML = html;
            });
    });
}
