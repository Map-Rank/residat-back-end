#!/bin/bash

ENVIRO=$DEPLOYMENT_GROUP_NAME
REGION="us-west-2"

# Get parameters and put it into .env file inside application root
sudo aws ssm get-parameter \
  --with-decryption \
  --name "/YPAY/$ENVIRO/ENVFILE" \
  --region $REGION \
  --with-decryption \
  --query Parameter.Value \
  --output text > /var/www/html/residat-back-end/.env


