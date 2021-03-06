IMAGE_NAME=cswl/xampp
CONTAINER_NAME=xamppy-docker
PUBLIC_WWW_DIR='~/web_pages'

winpty=""
[ "$OSTYPE" = "msys" ] && winpty="winpty "
root=""
[ "$OSTYPE" = "msys" ] && root="/"


echo "Running container '$CONTAINER_NAME' from image '$IMAGE_NAME'..."

docker start $CONTAINER_NAME > /dev/null 2> /dev/null || {
  echo "Creating new container..."
  ${winpty}docker run \
         --detach \
         --tty \
         -p 8086:80 \
         -p 3386:3306 \
         --name $CONTAINER_NAME \
         --mount "source=$CONTAINER_NAME-vol,destination=${root}/opt/lampp/var/mysql/" \
         --mount type=bind,src=${PWD},dst=/www \
      $IMAGE_NAME
}

if [ "$#" -eq  "0" ]; then
  docker exec --interactive --tty $CONTAINER_NAME bash
elif [ "$1" = "stop" ]; then
  docker stop $CONTAINER_NAME
else
  docker exec $CONTAINER_NAME $@
fi
