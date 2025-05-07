# ECS Cluster for the Laravel app
resource "aws_ecs_cluster" "lab_app_cluster" {
  name = "lab-app-cluster"
}
resource "aws_ecs_capacity_provider" "lab_app_ec2_capacity_provider" {
  name = "lab-app-capacity-provider-mod"

  auto_scaling_group_provider {
    auto_scaling_group_arn = aws_autoscaling_group.lab_ec2_autoscaling_group.arn
    managed_draining       = "ENABLED"


    managed_scaling {
      maximum_scaling_step_size = 1
      minimum_scaling_step_size = 1
      status                    = "ENABLED"
      target_capacity           = 100
    }
  }
}

resource "aws_ecs_cluster_capacity_providers" "lab_app_cluster_capacity_providers" {
  cluster_name = aws_ecs_cluster.lab_app_cluster.name

  capacity_providers = [
    aws_ecs_capacity_provider.lab_app_ec2_capacity_provider.name,
    "FARGATE", "FARGATE_SPOT"
  ]

  default_capacity_provider_strategy {
    capacity_provider = aws_ecs_capacity_provider.lab_app_ec2_capacity_provider.name
    weight            = 1
  }
}

# CloudWatch Log Group for container logs
resource "aws_cloudwatch_log_group" "app_logs" {
  name              = "/ecs/lab-final"
  retention_in_days = 14 # retain logs for 2 weeks (adjust as needed)
}

