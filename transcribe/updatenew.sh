
if [ -z "$1" ]; then
    echo 'Provide a password for zikzok'
    exit 1
fi

for uniqid in $(bash getvideonames.sh "$1"); do
    if [ -z $(find tmpvideos -type f -name "$uniqid.txt") ]; then
        bash getvideo.sh "$1" "$uniqid"
        bash transcribe.sh "$uniqid"
        rm tmpvideos/"$uniqid"
        echo "Finished $uniqid"
    fi
done
