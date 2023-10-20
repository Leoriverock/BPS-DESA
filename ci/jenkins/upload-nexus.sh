#!/bin/bash

source "ci/jenkins/config.sh"

# Image Tags 
IMAGE_VERSION="$GIT_COMMIT_SHORT"
APP_IMAGE_TAG="$DOCKER_REGISTRY_UPLOAD/$APP_IMAGE_PATH:$IMAGE_VERSION"    
APP_IMAGE_TAG_LATEST="$DOCKER_REGISTRY_UPLOAD/$APP_IMAGE_PATH:latest"    

docker login -u "$DOCKER_USER" -p "$DOCKER_PASSWORD" "$DOCKER_REGISTRY_UPLOAD"

# Build Docker Image                                                                    
docker build --tag "$APP_IMAGE_TAG" \
	--build-arg NEXUS_USER --build-arg NEXUS_PASSWORD \
    --build-arg CERTS_IMAGE_TAG \
    -f "$DOCKERFILE_PATH" "$DOCKER_BUILD_PATH"
	
docker tag "$APP_IMAGE_TAG" "$APP_IMAGE_TAG_LATEST"

# Upload the Image to Nexus
docker push "$APP_IMAGE_TAG"
docker push "$APP_IMAGE_TAG_LATEST"

# Clean the local registry
docker rmi "$APP_IMAGE_TAG" "$APP_IMAGE_TAG_LATEST"


