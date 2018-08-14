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

    sizeValue = document.getElementsByName('form[size]')
    sizeValue[0].value = twig_size
}

// Messages

function closeMsg(msgButton) {
    var messageButton = document.getElementById(msgButton)
    messageButton.parentNode.parentNode.removeChild(messageButton.parentNode)
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
    param = "markdown=" + encodeURIComponent(document.getElementById('form_content').value)

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        document.getElementById('preview').innerHTML = this.responseText
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

    document.getElementById(subTabName).style.display = "grid";
}
