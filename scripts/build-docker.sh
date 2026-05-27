#!/bin/bash
# Docker build script for M2M-SIM application
# Usage: ./scripts/build-docker.sh [tag]

set -e  # Exit on any error

# Configuration
IMAGE_NAME="iyedeh/m2m-sim-app"
DEFAULT_TAG="latest"

# Use provided tag or default
TAG=${1:-$DEFAULT_TAG}
FULL_IMAGE_NAME="${IMAGE_NAME}:${TAG}"

echo "==============================================="
echo "    M2M-SIM Docker Build Script"
echo "==============================================="
echo "Building image: ${FULL_IMAGE_NAME}"
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Error: Docker is not running or not installed."
    exit 1
fi

# Build the Docker image
echo "Building Docker image..."
docker build -t "${FULL_IMAGE_NAME}" .

# Optional: Also tag as latest if not already
if [ "$TAG" != "latest" ]; then
    echo "Tagging as latest..."
    docker tag "${FULL_IMAGE_NAME}" "${IMAGE_NAME}:latest"
fi

echo ""
echo "==============================================="
echo "    SUCCESS: Docker image built successfully!"
echo "==============================================="
echo "Image: ${FULL_IMAGE_NAME}"
echo ""
echo "To push to Docker Hub:"
echo "  docker push ${FULL_IMAGE_NAME}"
if [ "$TAG" != "latest" ]; then
    echo "  docker push ${IMAGE_NAME}:latest"
fi
echo ""
echo "To run locally:"
echo "  docker compose up -d"
echo "==============================================="