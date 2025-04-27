# Create RDS instance under the subnet group
resource "aws_db_subnet_group" "lab_db_subnet_group" {
  subnet_ids  = [
    aws_subnet.lab_private_subnet_a.id,
    aws_subnet.lab_private_subnet_b.id
  ]
  name        = "db-subnet-group"
  description = "DB Subnet Group"

  tags = {
    Name = "DB-Subnet-Group"
  }
}
resource "aws_db_instance" "lab_rds_instance" {
  identifier = "lab-db"

  # MySQL settings
  apply_immediately = true
  multi_az          = false
  allocated_storage = 20
  storage_type      = "gp2"
  engine            = "postgres"
  engine_version    = "17"
  instance_class    = "db.t4g.micro"

  # Credentials
  db_name  = "cp_final"
  username = "cpuser"
  password = "cppassword"

  # Connectivity
  db_subnet_group_name = aws_db_subnet_group.lab_db_subnet_group.id
  vpc_security_group_ids = [
    aws_security_group.lab_rds_security_group.id
  ]

  depends_on = [aws_db_subnet_group.lab_db_subnet_group]
}

