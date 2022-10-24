
if [ -z "$1" ]; then
    echo "Usage: $0 <videoname>"
    exit 1
fi

cd tmpvideos
whisper "$1" --model large --language English
