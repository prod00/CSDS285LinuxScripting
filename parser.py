import json
import numpy as np
import sys


def main(argv):
    f = open("levels_data.json")
    data = json.loads(f.read())
    salaries = []

    position = sys.argv[1].lower()
    location = sys.argv[2].lower()
    yoe = int(sys.argv[3])

    for datum in data:
        if location in datum["location"].lower() and position in datum["title"].lower() and int(datum["yearsofexperience"]) == yoe:
            salaries.append(float(datum["totalyearlycompensation"]) * 1000)

    try:
        salary_range = f"mean={round(np.mean(salaries),2)}&zeroth={np.percentile(salaries, 1, axis = 0)}&twentieth={np.percentile(salaries, 25, axis = 0)}&fiftieth={np.percentile(salaries, 50, axis = 0)}&seventy_fifth={np.percentile(salaries, 75, axis = 0)}&one_hundreth={np.percentile(salaries, 99, axis =0)}&standard_deviation={round(np.std(salaries),2)}"

        print(salary_range)
        return salary_range
    except:
        print("Error")
        return "Error With Ians Crappy Code"


if __name__ == "__main__":
    main(sys.argv[1:])
