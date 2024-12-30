import sys
import json
from datetime import datetime, timedelta

# Load patient data from command line input
patient_data = json.loads(sys.argv[1])

# Dummy AI model - Replace this with your actual AI logic
predicted_arrival_time = datetime.now() + timedelta(minutes=15)
predicted_duration = 30  # Dummy duration

# Output predictions
output = {
    "arrival_time": predicted_arrival_time.strftime('%Y-%m-%d %H:%M:%S'),
    "duration": predicted_duration
}

print(json.dumps(output))
