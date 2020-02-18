IMAGE=nonfiction/bedrock

all:
	@echo "image shell push"

image:
	docker build --tag $(IMAGE) .

shell: 
	docker run --rm -it $(IMAGE) /bin/bash

push: 
	docker push $(IMAGE)
