
if [ -z "$1" ] ; then
    echo "Usage: $0 <pass>"
    exit 1
fi

# Get list of video transcripts on zikzok
havetxt=$(sshpass -p "$1" ssh zikzok72@zikzok.org 'ls ~/public_html/transcribe/tmpvideos')

# Upload the files we have locally but are not on zikzok yet
for uniqidext in $(bash getvideonames.sh "$1"); do
    if ! [ -z $(find tmpvideos -type f -name "$uniqidext.txt") ]; then
        if ! [[ "$havetxt" =~ "$uniqidext.txt" ]]; then
            echo "Uploading  $uniqidext.txt"
            sshpass -p "$1" scp "tmpvideos/$uniqidext.txt" zikzok72@zikzok.org:~/public_html/transcribe/tmpvideos
            sshpass -p "$1" scp "tmpvideos/$uniqidext.srt" zikzok72@zikzok.org:~/public_html/transcribe/timing
        fi
    fi
done
