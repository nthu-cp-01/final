# How to get image id:
# https://docs.aws.amazon.com/AmazonECS/latest/developerguide/retrieve-ecs-optimized_AMI.html
locals {
  ec2_launch_script = <<EOF
#!/bin/bash
echo ECS_CLUSTER=${aws_ecs_cluster.lab_app_cluster.name} >> /etc/ecs/ecs.config
EOF
}

resource "aws_launch_template" "lab_ec2_template" {
  name_prefix   = "Lab_EC2_Launch_Template"
  image_id      = "ami-04c73aa7cd73f7830"
  instance_type = "t3.micro"

  key_name               = "vockey"
  vpc_security_group_ids = [aws_security_group.ecs_sg.id]
  iam_instance_profile {
    name = "LabInstanceProfile"
  }

  block_device_mappings {
    device_name = "/dev/xvda"
    ebs {
      volume_size = 30
      volume_type = "gp2"
    }
  }

  tag_specifications {
    resource_type = "instance"
    tags = {
      Name = "ecs-instance"
    }
  }

  user_data = base64encode(local.ec2_launch_script)
}

resource "aws_autoscaling_group" "lab_ec2_autoscaling_group" {
  vpc_zone_identifier = [
    aws_subnet.lab_private_subnet_a.id,
    aws_subnet.lab_private_subnet_b.id
  ]
  desired_capacity = 1
  max_size         = 1
  min_size         = 1

  launch_template {
    id      = aws_launch_template.lab_ec2_template.id
    version = "$Latest"
  }

  tag {
    key                 = "AmazonECSManaged"
    value               = true
    propagate_at_launch = true
  }
}
