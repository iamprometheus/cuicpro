#!/bin/bash

# Set the folder and zip file names
FOLDER="cuicpro"
ZIP_FILE="cuicpro.zip"

# Check if the folder exists
if [ ! -d "$FOLDER" ]; then
  echo "❌ Folder '$FOLDER' does not exist. Nothing to zip."
  exit 1
fi

# Remove old zip file if it exists
if [ -f "$ZIP_FILE" ]; then
  echo "Removing existing $ZIP_FILE..."
  rm "$ZIP_FILE"
fi

# Create the zip archive
echo "Zipping $FOLDER into $ZIP_FILE..."
zip -r "$ZIP_FILE" "$FOLDER" > /dev/null

# Delete the folder
rm -rf "$FOLDER"

echo "✅ Zipping complete: $ZIP_FILE"