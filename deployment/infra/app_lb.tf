# Application Load Balancer
resource "aws_lb" "lab_app_lb" {
  name               = "lab-app-lb"
  load_balancer_type = "application"
  subnets = [
    aws_subnet.lab_public_subnet_a.id,
    aws_subnet.lab_public_subnet_b.id
  ]
  security_groups = [aws_security_group.lab_app_security_group.id]
}

# Target Group for ECS tasks
resource "aws_lb_target_group" "lab_app_tg" {
  name        = "lab-app-tg"
  port        = 80
  protocol    = "HTTP"
  target_type = "ip" # Required by Fargate
  vpc_id      = aws_vpc.lab_vpc.id
  health_check {
    path                = "/up"
    interval            = 30
    timeout             = 5
    unhealthy_threshold = 3
    healthy_threshold   = 2
    matcher             = "200"
  }
}

# Listener to forward HTTP requests to the target group
resource "aws_lb_listener" "lab_app_lb_listener" {
  load_balancer_arn = aws_lb.lab_app_lb.arn
  port              = 80
  protocol          = "HTTP"
  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.lab_app_tg.arn
  }
}
