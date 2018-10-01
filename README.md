
idea: automatizzare upload archivio su Artifactory

usage:

./bin/packager [--name=company/project --username=USERNAME --password=PASSWORD --repository=ARTIFACTORY --version=1.0.0 --composer=./composer.json --config=./packager.yml]


file di configurazione:

### packager.yml
packager:
    composer: ./composer.json
    name: ~
    repositories:
        ARTIFACTORY:
            endpoint: ~ (required)
            username: ~
            password: ~
