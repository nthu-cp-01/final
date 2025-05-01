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

output "app_endpoint" {
  value       = aws_lb.lab_app_lb.dns_name
  description = "The endpoint of the application load balancer"
}
