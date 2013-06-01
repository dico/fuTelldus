cm160server > /var/log/owl &
# Get its PID
PID=$!
echo $PID
# Wait for 2 seconds
sleep 15
# Kill it
kill -9  $PID
