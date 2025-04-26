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

# Import the cost_saving module
module "cost_saving" {
  source             = "./cost_saving"
  billing_thresholds = [1, 5, 10, 15, 20, 25, 30, 35, 40, 45] # Example thresholds in USD
  notification_emails = [
    "j.s.li@gapp.nthu.edu.tw",
  ]
}

data "aws_caller_identity" "current" {}
data "aws_iam_role" "lab_role" {
  name = "LabRole"
}
