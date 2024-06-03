import json
import random
import logging

logger = logging.getLogger()
logger.setLevel(logging.INFO)

def lambda_handler(event, context):
    """
    Lambda function for returning a random hobby.
    
    Args:
        event (dict): Not used
        context (LambdaContext): Not used.
        
    Returns:
        dict: JSON.
    """
    try:
        hobbies = [
            "Gaming",
            "Reading",
            "Editing Videos",
            "Painting",
            "Drawing",
            "Writing",
            "Cooking",
            "Nothing at all, just chill",
            "Fishing",
            "Mountain Biking"
        ]
        
        hobby = random.choice(hobbies)
        
        response = {
            "statusCode": 200,
            "body": json.dumps({
                "message": "You should try next: ",
                "hobby": hobby
            })
        }
        
        logger.info(f"Selected hobby: {hobby}")
        return response
    
    except Exception as e:
        logger.error(f"Erro while trying to select a hobby: {str(e)}")
        return {
            "statusCode": 500,
            "body": json.dumps({
                "message": "Error while trying to select a hobby."
            })
        }
