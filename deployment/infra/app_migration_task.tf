# ECS Task Definition for Laravel app
resource "aws_ecs_task_definition" "laravel_run_migration" {
  family                   = "lab-laravel-run-migration" # task family name
  cpu                      = "256"                       # 1/4 vCPU
  memory                   = "512"                       # 0.5 GB
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  execution_role_arn       = data.aws_iam_role.lab_role.arn
  task_role_arn            = data.aws_iam_role.lab_role.arn
  container_definitions = jsonencode([
    {
      name      = "final-app-migration"
      image     = "ghcr.io/nthu-cp-01/final-migration:main"
      essential = true
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
        }
      ]
      environment = [
        # Application
        { name = "APP_NAME", value = "AWS" },
        { name = "APP_ENV", value = "local" },
        { name = "APP_KEY", value = "base64:3vVjC2JIukr6/8WQrd3cxYsdn4DKNn35h3/t/uIaG5M=" },
        { name = "APP_URL", value = "http://localhost" },
        { name = "APP_DEBUG", value = "true" },
        { name = "APP_LOCALE", value = "en" },
        { name = "APP_FALLBACK_LOCALE", value = "en" },
        { name = "APP_TIMEZONE", value = "UTC+8" },

        # Database
        { name = "DB_CONNECTION", value = "pgsql" },
        { name = "DB_HOST", value = aws_db_instance.lab_rds_instance.address },
        { name = "DB_PORT", value = tostring(aws_db_instance.lab_rds_instance.port) },
        { name = "DB_DATABASE", value = aws_db_instance.lab_rds_instance.db_name },
        { name = "DB_USERNAME", value = aws_db_instance.lab_rds_instance.username },
        { name = "DB_PASSWORD", value = "cppassword" },

        # Cache
        { name = "REDIS_CLIENT", value = "predis" },
        { name = "REDIS_HOST", value = aws_elasticache_replication_group.lab_redis.primary_endpoint_address },
        { name = "REDIS_PORT", value = "6379" },
        { name = "REDIS_USERNAME", value = "default" },
        { name = "REDIS_PASSWORD", value = "default" },
        { name = "CACHE_DRIVER", value = "redis" },
        { name = "QUEUE_CONNECTION", value = "redis" },

        # Session
        { name = "SESSION_DRIVER", value = "redis" },
        { name = "SESSION_CONNECTION", value = "default" },
        { name = "SESSION_LIFETIME", value = "120" },
        { name = "SESSION_ENCRYPT", value = "false" },
        { name = "SESSION_PATH", value = "/" },
        { name = "SESSION_DOMAIN", value = "null" },

        # Storage(swap with S3 maybe?)
        { name = "FILESYSTEM_DISK", value = "local" },
      ]
      logConfiguration = {
        logDriver = "awslogs",
        options = {
          "awslogs-region"        = "us-east-1",
          "awslogs-group"         = aws_cloudwatch_log_group.app_logs.name,
          "awslogs-stream-prefix" = "laravel-migration",
        }
      }
    }
  ])
  runtime_platform {
    operating_system_family = "LINUX"
    cpu_architecture        = "X86_64"
  }
}

resource "aws_scheduler_schedule" "migration_once" {
  name                         = "laravel-db-migration-once"
  description                  = "One-time ECS task to run Laravel migrations"
  schedule_expression          = "at(${formatdate("YYYY-MM-DD'T'hh:mm:ss", timeadd(timestamp(), "2m"))})" # e.g. at(2025-04-28T01:00:00) for one-time schedule
  schedule_expression_timezone = "UTC"
  flexible_time_window {
    mode = "OFF" # Run at the exact specified time (no window)&#8203;:contentReference[oaicite:2]{index=2}
  }
  target {
    arn      = aws_ecs_cluster.lab_app_cluster.arn
    role_arn = data.aws_iam_role.lab_role.arn

    ecs_parameters { # ECS RunTask parameters&#8203;:contentReference[oaicite:5]{index=5}
      task_definition_arn = aws_ecs_task_definition.laravel_run_migration.arn
      launch_type         = "FARGATE"
      platform_version    = "1.4.0" # Use latest Fargate platform
      task_count          = 1

      network_configuration {    # Networking for awsvpc mode&#8203;:contentReference[oaicite:6]{index=6}
        assign_public_ip = false # Task in private subnets
        subnets = [
          aws_subnet.lab_private_subnet_a.id,
          aws_subnet.lab_private_subnet_b.id
        ]
        security_groups = [aws_security_group.ecs_sg.id]
      }
    }
  }

  depends_on = [aws_ecs_task_definition.laravel_run_migration, aws_db_instance.lab_rds_instance]
}
