if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 <pass> <videoname>"
    exit 1
fi

sshpass -p "$1" scp zikzok72@zikzok.org:~/public_html/videos/"$2" tmpvideos
