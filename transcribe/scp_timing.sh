
if [ -z "$1" ] ; then
    echo "Usage: $0 <pass>"
    exit 1
fi

# Get list of video timings on zikzok
havetimings=$(sshpass -p "$1" ssh zikzok72@zikzok.org 'ls ~/public_html/transcribe/timing')

# Get a list of timings that we have locally
# Upload those we don't
for timing in $(ls timing/*.srt); do
    base=$(basename $timing)
    if ! [[ "$havetimings" =~ "$base" ]]; then
        echo "Uploading $timing"
        sshpass -p "$1" scp "$timing" zikzok72@zikzok.org:~/public_html/transcribe/timing
    fi
done
