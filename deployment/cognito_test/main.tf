terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

# Configure the AWS Provider
provider "aws" {
  region = "us-east-1"
}
provider "tls" {}

data "aws_caller_identity" "current" {}
data "aws_iam_role" "lab_role" {
  name = "LabRole"
}

output "client_id" {
  value       = aws_cognito_user_pool_client.lab_user_pool_client.id
  description = "The client ID of the Cognito User Pool Client"
}

output "client_secret" {
  sensitive   = true
  value       = aws_cognito_user_pool_client.lab_user_pool_client.client_secret
  description = "The client secret of the Cognito User Pool Client"
}

output "client_endpoint" {
  value = format(
    "https://%s.auth.%s.amazoncognito.com",
    aws_cognito_user_pool_domain.lab_user_pool_domain.domain,
    "us-east-1"
  )
  description = "The endpoint of the Cognito User Pool"
}
