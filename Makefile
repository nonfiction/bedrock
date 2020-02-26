IMAGE=nonfiction/bedrock

all:
	@echo "build push shell"

build:
	DOCKER_BUILDKIT=1 docker build --tag $(IMAGE) .

push: 
	docker push $(IMAGE)

shell: 
	docker run --rm -it $(IMAGE) /bin/bash
