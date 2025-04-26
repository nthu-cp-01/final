# Create VPC and IGW
resource "aws_vpc" "lab_vpc" {
  cidr_block = "10.0.0.0/16"

  tags = {
    Name = "lab-vpc"
  }
}

# Create security group for RDS
resource "aws_security_group" "lab_rds_security_group" {
  name        = "DB Security Group"
  description = "Permit access from App Security Group"
  vpc_id      = aws_vpc.lab_vpc.id
}
resource "aws_vpc_security_group_ingress_rule" "lab_sg_allow_rds" {
  security_group_id            = aws_security_group.lab_rds_security_group.id
  referenced_security_group_id = aws_security_group.ecs_sg.id
  from_port                    = 3306
  to_port                      = 3306
  ip_protocol                  = "tcp"
}

# Create security group for cache
resource "aws_security_group" "lab_cache_security_group" {
  name        = "Cache Security Group"
  description = "Permit access from App Security Group"
  vpc_id      = aws_vpc.lab_vpc.id
}
resource "aws_vpc_security_group_ingress_rule" "lab_sg_allow_cache" {
  security_group_id            = aws_security_group.lab_cache_security_group.id
  referenced_security_group_id = aws_security_group.ecs_sg.id
  from_port                    = 6379
  to_port                      = 6379
  ip_protocol                  = "tcp"
}

# Create security group for app
resource "aws_security_group" "lab_app_security_group" {
  name        = "App Security Group"
  description = "Permit access from anywhere"
  vpc_id      = aws_vpc.lab_vpc.id
}
resource "aws_vpc_security_group_ingress_rule" "lab_sg_allow_app" {
  security_group_id = aws_security_group.lab_app_security_group.id

  cidr_ipv4   = "0.0.0.0/0"
  from_port   = 80
  to_port     = 80
  ip_protocol = "tcp"
}
resource "aws_vpc_security_group_ingress_rule" "lab_sg_allow_app_tls" {
  security_group_id = aws_security_group.lab_app_security_group.id

  cidr_ipv4   = "0.0.0.0/0"
  from_port   = 443
  to_port     = 443
  ip_protocol = "tcp"
}

# Security Group for ECS Tasks (private)
resource "aws_security_group" "ecs_sg" {
  name        = "ECS Security Group"
  description = "Allow traffic from ALB to ECS tasks"
  vpc_id      = aws_vpc.lab_vpc.id
}
resource "aws_vpc_security_group_ingress_rule" "ecs_ingress_from_alb" {
  security_group_id            = aws_security_group.ecs_sg.id
  referenced_security_group_id = aws_security_group.lab_app_security_group.id

  from_port   = 80
  to_port     = 80
  ip_protocol = "tcp"
  description = "Allow ALB to reach ECS tasks on app port"
}
