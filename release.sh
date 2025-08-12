#!/bin/bash

# Set source and destination directories
SOURCE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST_DIR="$SOURCE_DIR/cuicpro"

# List of folders and file to copy
ITEMS=("build" "dashboard" "frontend" "model" "cuicpro.php")

# Create destination directory if it doesn't exist
mkdir -p "$DEST_DIR"

# Loop through each item and copy it
for item in "${ITEMS[@]}"; do
    SRC_PATH="$SOURCE_DIR/$item"
    DEST_PATH="$DEST_DIR/$item"

    if [ -e "$SRC_PATH" ]; then
        echo "Copying $item..."
        if [ -d "$SRC_PATH" ]; then
            cp -r "$SRC_PATH" "$DEST_DIR"
        else
            cp "$SRC_PATH" "$DEST_DIR"
        fi
    else
        echo "Skipping $item - not found."
    fi
done

echo "✅ Copy complete."
