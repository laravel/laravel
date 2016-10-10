#!/bin/sh
if [ -z $ES_VERSION ]; then
    echo "No ES_VERSION specified";
    exit 1;
fi;


killall java 2>/dev/null


echo "Downloading Elasticsearch v${ES_VERSION}-SNAPSHOT..."


# Sourced from https://github.com/elastic/ci/blob/master/client_tests_urls.prop
# Note: currently URLs are all the same format, but sometimes it changes
# TODO automate this
case $ES_VERSION in
    '2.2') ES_URL="http://s3-eu-west-1.amazonaws.com/build.eu-west-1.elastic.co/origin/2.2/nightly/JDK7/elasticsearch-latest-SNAPSHOT.zip" ;;
	'2.1') ES_URL="http://s3-eu-west-1.amazonaws.com/build.eu-west-1.elastic.co/origin/2.1/nightly/JDK7/elasticsearch-latest-SNAPSHOT.zip" ;;
  '2.0') ES_URL="http://s3-eu-west-1.amazonaws.com/build.eu-west-1.elastic.co/origin/2.0/nightly/JDK7/elasticsearch-latest-SNAPSHOT.zip" ;;
  '1.7') ES_URL="http://s3-eu-west-1.amazonaws.com/build.eu-west-1.elastic.co/origin/1.7/nightly/JDK7/elasticsearch-latest-SNAPSHOT.zip" ;;
  '1.6') ES_URL="http://s3-eu-west-1.amazonaws.com/build.eu-west-1.elastic.co/origin/1.6/nightly/JDK7/elasticsearch-latest-SNAPSHOT.zip" ;;
esac

curl -L -O $ES_URL
unzip "elasticsearch-latest-SNAPSHOT.zip"

echo "Adding repo to config..."
find . -name "elasticsearch.yml" | while read TXT ; do echo 'repositories.url.allowed_urls: ["http://*"]' >> $TXT ; done
find . -name "elasticsearch.yml" | while read TXT ; do echo 'path.repo: ["/tmp"]' >> $TXT ; done



echo "Starting Elasticsearch v${ES_VERSION}"
./elasticsearch-*/bin/elasticsearch \
    -Des.network.host=localhost \
    -Des.discovery.zen.ping.multicast.enabled=false \
    -Des.discovery.zen.ping_timeout=1s \
    -Des.http.port=9200 \
    -Des.node.testattr=test \
    -d

sleep 3
