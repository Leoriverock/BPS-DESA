#!/bin/bash

source "ci/jenkins/config.sh"

docker login -u "$DOCKER_USER" -p "$DOCKER_PASSWORD" "$DOCKER_REGISTRY_DOWNLOAD"

docker build \
    --build-arg NEXUS_USER --build-arg NEXUS_PASSWORD \
    --build-arg CERTS_IMAGE_TAG \
    -t "$APP_BUILDER_IMAGE_TAG_LATEST" \
    -f "$DOCKERFILE_PATH" "$DOCKER_BUILD_PATH"

