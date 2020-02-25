IMAGE=nonfiction/bedrock

command:
	@echo "docker-compose up web"

build:
	DOCKER_BUILDKIT=1 docker build --tag $(IMAGE) .

shell: 
	docker run --rm -it $(IMAGE) /bin/bash

push: 
	docker push $(IMAGE)
