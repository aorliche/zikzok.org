
if [ -z "$1" ]; then
    echo 'Provide a password for zikzok'
    exit 1
fi

sshpass -p "$1" ssh zikzok72@zikzok.org 'ls public_html/videos'
