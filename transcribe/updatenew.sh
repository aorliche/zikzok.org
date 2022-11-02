
if [ -z "$1" ]; then
    echo 'Provide a password for zikzok'
    exit 1
fi

badlist="62f85cc96c26e.webm 62f85cc96c2e2.webm 62f85cc96cc8d.mp4 62f85cc96cce3.webm"


for uniqidext in $(bash getvideonames.sh "$1"); do
    if [ -z $(find tmpvideos -type f -name "$uniqidext.txt") ]; then
        if [[ "$badlist" =~ "$uniqidext" ]]; then
            continue
        fi
        echo "Beginning $uniqidext"
        bash getvideo.sh "$1" "$uniqidext"
        bash transcribe.sh "$uniqidext"
        rm tmpvideos/"$uniqidext"
        echo "Finished $uniqidext"
    fi
done
