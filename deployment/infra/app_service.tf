# ECS Service to run the Laravel task
resource "aws_ecs_service" "web_service" {
  name                   = "lab-final-laravel-service"
  cluster                = aws_ecs_cluster.lab_app_cluster.id
  task_definition        = aws_ecs_task_definition.laravel_app.arn
  enable_execute_command = true
  launch_type            = "FARGATE"
  platform_version       = "1.4.0" # latest Fargate platform (supports secrets fetch, etc.)
  desired_count          = 1

  network_configuration {
    subnets = [
      aws_subnet.lab_private_subnet_a.id,
      aws_subnet.lab_private_subnet_b.id
    ]

    security_groups  = [aws_security_group.ecs_sg.id]
    assign_public_ip = false
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.lab_app_tg.arn
    container_name   = "final-app"
    container_port   = 80
  }

  depends_on = [aws_lb_listener.lab_app_lb_listener]
}
