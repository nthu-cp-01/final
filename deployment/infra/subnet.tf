# Create all private subnets
resource "aws_subnet" "lab_private_subnet_a" {
    vpc_id            = aws_vpc.lab_vpc.id
    cidr_block        = "10.0.2.0/24"
    availability_zone = "us-east-1a"

    tags = {
        Name = "lab-subnet-private1-us-east-1a"
    }
}
resource "aws_subnet" "lab_private_subnet_b" {
    vpc_id            = aws_vpc.lab_vpc.id
    cidr_block        = "10.0.3.0/24"
    availability_zone = "us-east-1b"

    tags = {
        Name = "lab-subnet-private1-us-east-1b"
    }
}

# Create public subnet and IGW
resource "aws_internet_gateway" "lab_igw" {
  vpc_id = aws_vpc.lab_vpc.id

  tags = {
    Name = "lab-igw"
  }
}
resource "aws_subnet" "lab_public_subnet_a" {
  vpc_id            = aws_vpc.lab_vpc.id
  cidr_block        = "10.0.0.0/24"
  availability_zone = "us-east-1a"

  tags = {
    Name = "lab-subnet-public1-us-east-1a"
  }
}
resource "aws_route_table" "lab_public_route_table" {
  vpc_id = aws_vpc.lab_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.lab_igw.id
  }

  tags = {
    Name = "lab-rtb-public"
  }
}
resource "aws_route_table_association" "lab_add_rtb_public_a" {
  subnet_id      = aws_subnet.lab_public_subnet_a.id
  route_table_id = aws_route_table.lab_public_route_table.id
}
